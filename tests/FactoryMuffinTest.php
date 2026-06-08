<?php

/*
 * This file is part of Factory Muffin.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 * (c) Scott Robertson <scottymeuk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\FactoryMuffin\Exceptions\DefinitionNotFoundException;
use League\FactoryMuffin\FactoryMuffin;

/**
 * This is factory muffin test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 * @author Scott Robertson <scottymeuk@gmail.com>
 */
class FactoryMuffinTest extends AbstractTestCase
{
    public function testDefaultingToFaker()
    {
        $obj = static::$fm->instance('FakerDefaultingModelStub');
        $this->assertIsArray($obj->card);
        $this->assertArrayHasKey('type', $obj->card);
        $this->assertArrayHasKey('number', $obj->card);
        $this->assertArrayHasKey('name', $obj->card);
        $this->assertArrayHasKey('expirationDate', $obj->card);

        if (version_compare(PHP_VERSION, '7.1.0') >= 0 && !defined('HHVM_VERSION')) {
            $this->assertSame('https://via.placeholder.com/400x600', substr($obj->image, 0, 35));
        } else {
            $this->assertSame('https://lorempixel.com/400/600', substr($obj->image, 0, 30));
        }

        $this->assertNotEquals('unique::text', $obj->unique_text);
        $this->assertNotEquals('optional::text', $obj->optional_text);
    }

    public function testGetIds()
    {
        $obj = static::$fm->instance('IdTestModelStub');

        $this->assertSame(1, $obj->modelGetKey);
        $this->assertSame(1, $obj->modelPk);
        $this->assertSame(1, $obj->model_id);
        $this->assertNull($obj->model_null);
    }

    public function testShouldMakeSimpleCalls()
    {
        $obj = static::$fm->instance('ComplexModelStub');

        $expected = gmdate('Y-m-d', strtotime('+40 days'));

        $this->assertSame($expected, $obj->future);
    }

    public function testFakerDefaultBoolean()
    {
        $obj = static::$fm->instance('MainModelStub');

        $this->assertIsBool($obj->boolean, "Asserting {$obj->boolean} is a boolean");
    }

    public function testFakerDefaultLatitude()
    {
        $obj = static::$fm->instance('MainModelStub');

        $this->assertGreaterThanOrEqual(-90, $obj->lat);
        $this->assertLessThanOrEqual(90, $obj->lat);
    }

    public function testFakerDefaultLongitude()
    {
        $obj = static::$fm->instance('MainModelStub');

        $this->assertGreaterThanOrEqual(-180, $obj->lon);
        $this->assertLessThanOrEqual(180, $obj->lon);
    }

    public function testShouldThrowDefinitionNotFoundException()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\DefinitionNotFoundException::class);

        try {
            static::$fm->instance($model = 'ModelWithNoFactoryClassStub');
        } catch (DefinitionNotFoundException $e) {
            $this->assertSame("The model definition '$model' is undefined.", $e->getMessage());
            $this->assertSame($model, $e->getDefinitionName());

            throw $e;
        }
    }

    public function testShouldAcceptClosureAsAttributeFactory()
    {
        $obj = static::$fm->instance('MainModelStub');
        $this->assertSame('just a string', $obj->text_closure);
    }

    public function testCanCreateFromStaticMethod()
    {
        $obj = static::$fm->instance('ModelWithStaticMethodFactory');

        $this->assertSame('just a string', $obj->string);
        $this->assertInstanceOf('ModelWithStaticMethodFactory', $obj->data['object']);
        $this->assertFalse($obj->data['saved']);
    }

    public function testSetAttributeUsingSetter()
    {
        $obj = static::$fm->instance('SetterTestModelWithSetter');
        $this->assertSame('Jack Sparrow', $obj->getName());
    }

    public function testCanDetectNonPublicSetters()
    {
        $obj = static::$fm->instance('SetterTestModelWithNonPublicSetter');
        $this->assertSame('Jack Sparrow', $obj->getName());
    }

    public function testCamelization()
    {
        $var = FactoryMuffin::camelize('foo_bar');
        $this->assertSame('fooBar', $var);

        $var = FactoryMuffin::camelize('foo');
        $this->assertSame('foo', $var);

        $var = FactoryMuffin::camelize('foo_bar2_bar');
        $this->assertSame('fooBar2Bar', $var);
    }
}

#[\AllowDynamicProperties]
class MainModelStub
{
    public function save()
    {
        $this->id = date('U');

        return true;
    }

    public function delete()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class FakerDefaultingModelStub extends MainModelStub
{
    //
}

#[\AllowDynamicProperties]
class ComplexModelStub
{
    public static function fortyDaysFromNow()
    {
        return gmdate('Y-m-d', strtotime('+40 days'));
    }

    public function save()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class ModelWithNoFactoryClassStub
{
    public function save()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class IdTestModelStub
{
    public function save()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class IdTestModelGetKeyStub
{
    public function getKey()
    {
        return 1;
    }

    public function save()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class IdTestModelPkStub
{
    public function pk()
    {
        return 1;
    }

    public function save()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class IdTestModelIdStub
{
    public $_id = 1;

    public function save()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class IdTestModelNullStub
{
    public function save()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class ModelWithStaticMethodFactory
{
    public function save()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class SetterTestModelWithSetter
{
    private $name;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}

#[\AllowDynamicProperties]
class SetterTestModelWithNonPublicSetter
{
    private $name;

    public function __set($key, $value)
    {
        if ($key === 'name') {
            $this->setName($value);
        } else {
            $this->$key = $value;
        }
    }

    private function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
