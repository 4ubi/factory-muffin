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

use League\FactoryMuffin\Exceptions\DeletingFailedException;
use League\FactoryMuffin\Exceptions\SaveFailedException;
use League\FactoryMuffin\Exceptions\SaveMethodNotFoundException;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Stores\ModelStore;

/**
 * This is save and delete test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 * @author Scott Robertson <scottymeuk@gmail.com>
 */
class SaveAndDeleteTest extends AbstractTestCase
{
    public function testShouldCreateAndDelete()
    {
        $obj = static::$fm->create('ModelThatWillSaveStub');
        $reflection = new ReflectionClass(static::$fm);
        $store = $reflection->getProperty('store');
        $value = $store->getValue(static::$fm);

        $this->assertTrue($value->isSaved($obj));
        $this->assertTrue(is_numeric($obj->id));
        $this->assertIsArray($value->saved());
        $this->assertCount(1, $value->saved());
        $this->assertCount(0, $value->pending());

        static::$fm->deleteSaved();
    }

    public function testShouldNotSave()
    {
        $obj = static::$fm->instance('ModelThatWillSaveStub');
        $reflection = new ReflectionClass(static::$fm);
        $store = $reflection->getProperty('store');
        $value = $store->getValue(static::$fm);

        $this->assertCount(0, $value->saved());
        $this->assertCount(0, $value->pending());
        $this->assertFalse($value->isSaved($obj));
    }

    public function testShouldThrowExceptionAfterSaveMethodRename()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\SaveMethodNotFoundException::class);

        static::$fm = new FactoryMuffin(new ModelStore('foo'));
        static::$fm->loadFactories(__DIR__.'/factories');

        try {
            static::$fm->create($model = 'ModelThatWillSaveStub');
        } catch (SaveMethodNotFoundException $e) {
            $this->assertSame("The save method 'foo' was not found on the model: '$model'.", $e->getMessage());
            $this->assertSame($model, $e->getModelClass());
            $this->assertSame('foo', $e->getMethodName());
            $this->reload();

            throw $e;
        }

        $this->reload();
    }

    public function testShouldThrowExceptionAfterDeleteMethodRename()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\DeletingFailedException::class);

        static::$fm = new FactoryMuffin(new ModelStore(null, 'bar'));
        static::$fm->loadFactories(__DIR__.'/factories');

        static::$fm->create($model = 'ModelThatWillSaveStub');

        try {
            static::$fm->deleteSaved();
        } catch (DeletingFailedException $e) {
            $exceptions = $e->getExceptions();
            $this->assertSame('We encountered 1 problem while trying to delete the saved models.', $e->getMessage());
            $this->assertSame("The delete method 'bar' was not found on the model: '$model'.", $exceptions[0]->getMessage());
            $this->assertSame($model, $exceptions[0]->getModelClass());
            $this->assertSame('bar', $exceptions[0]->getMethodName());
            $this->reload();

            throw $e;
        }

        $this->reload();
    }

    public function testShouldThrowExceptionOnModelSaveFailure()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\SaveFailedException::class);

        try {
            static::$fm->create($model = 'ModelThatFailsToSaveStub');
        } catch (SaveFailedException $e) {
            $this->assertSame("We could not save the model: '$model'.", $e->getMessage());
            $this->assertSame($model, $e->getModelClass());
            $this->assertNull($e->getValidationErrors());

            throw $e;
        }
    }

    public function testShouldThrowExceptionOnModelDeleteFailure()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\DeletingFailedException::class);

        try {
            static::$fm->create($model = 'ModelThatFailsToDeleteStub');
            static::$fm->deleteSaved();
        } catch (DeletingFailedException $e) {
            $exceptions = $e->getExceptions();
            $this->assertSame('We encountered 1 problem while trying to delete the saved models.', $e->getMessage());
            $this->assertSame("We could not delete the model: '$model'.", $exceptions[0]->getMessage());

            throw $e;
        }
    }

    public function testShouldAlsoThrowExceptionOnModelDeleteFailure()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\DeletingFailedException::class);

        try {
            static::$fm->create($model = 'ModelThatAlsoFailsToDeleteStub');
            static::$fm->deleteSaved();
        } catch (DeletingFailedException $e) {
            $exceptions = $e->getExceptions();
            $this->assertSame('We encountered 1 problem while trying to delete the saved models.', $e->getMessage());
            $this->assertSame('OH NOES!', $exceptions[0]->getMessage());

            throw $e;
        }
    }

    public function testShouldThrowExceptionWithoutSaveMethod()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\SaveMethodNotFoundException::class);

        try {
            static::$fm->create($model = 'ModelWithNoSaveMethodStub');
        } catch (SaveMethodNotFoundException $e) {
            $this->assertSame("The save method 'save' was not found on the model: '$model'.", $e->getMessage());
            $this->assertSame($model, $e->getModelClass());
            $this->assertSame('save', $e->getMethodName());

            throw $e;
        }
    }

    public function testShouldThrowExceptionWithoutDeleteMethod()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\DeletingFailedException::class);

        try {
            static::$fm->create($model = 'ModelWithNoDeleteMethodStub');
            static::$fm->deleteSaved();
        } catch (DeletingFailedException $e) {
            $exceptions = $e->getExceptions();
            $this->assertSame('We encountered 1 problem while trying to delete the saved models.', $e->getMessage());
            $this->assertSame("The delete method 'delete' was not found on the model: '$model'.", $exceptions[0]->getMessage());
            $this->assertSame($model, $exceptions[0]->getModelClass());
            $this->assertSame('delete', $exceptions[0]->getMethodName());

            throw $e;
        }
    }

    public function testShouldThrowExceptionWithValidationErrors()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\SaveFailedException::class);

        try {
            static::$fm->create($model = 'ModelWithValidationErrorsStub');
        } catch (SaveFailedException $e) {
            $this->assertSame("Failed to save! We could not save the model: '$model'.", $e->getMessage());
            $this->assertSame($model, $e->getModelClass());
            $this->assertSame('Failed to save!', $e->getValidationErrors());

            throw $e;
        }
    }

    public function testShouldThrowExceptionWithBadValidationErrors()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\SaveFailedException::class);

        try {
            static::$fm->create($model = 'ModelWithBadValidationErrorsStub');
        } catch (SaveFailedException $e) {
            $this->assertSame("Oh noes. We could not save the model: '$model'.", $e->getMessage());
            $this->assertSame($model, $e->getModelClass());
            $this->assertSame('Oh noes.', $e->getValidationErrors());

            throw $e;
        }
    }

    public function testShouldThrowMultipleDeletionExceptions()
    {
        $this->expectException(\League\FactoryMuffin\Exceptions\DeletingFailedException::class);

        try {
            static::$fm->create($model = 'ModelWithNoDeleteMethodStub');
            static::$fm->create('ModelThatAlsoFailsToDeleteStub');
            static::$fm->deleteSaved();
        } catch (DeletingFailedException $e) {
            $exceptions = $e->getExceptions();
            $this->assertSame('We encountered 2 problems while trying to delete the saved models.', $e->getMessage());
            $this->assertSame('OH NOES!', $exceptions[0]->getMessage());
            $this->assertSame("The delete method 'delete' was not found on the model: '$model'.", $exceptions[1]->getMessage());
            $this->assertSame($model, $exceptions[1]->getModelClass());
            $this->assertSame('delete', $exceptions[1]->getMethodName());
            $this->assertIsArray($e->getExceptions());
            $this->assertCount(2, $e->getExceptions());

            throw $e;
        }
    }

    public function testModelIsNotPersistedASecondTimeIfClosureReturnsFalse()
    {
        $obj1 = static::$fm->create('no return:ModelWithTrackedSaves');
        $obj2 = static::$fm->create('return true:ModelWithTrackedSaves');
        $obj3 = static::$fm->create('return false:ModelWithTrackedSaves');

        $this->assertEquals(2, $obj1->saveCounter);
        $this->assertEquals(2, $obj2->saveCounter);
        $this->assertEquals(1, $obj3->saveCounter);
    }
}

#[\AllowDynamicProperties]
class ModelThatWillSaveStub
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
class ModelThatFailsToSaveStub
{
    public function save()
    {
        return false;
    }

    public function delete()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class ModelThatFailsToDeleteStub
{
    public function save()
    {
        return true;
    }

    public function delete()
    {
        return false;
    }
}

#[\AllowDynamicProperties]
class ModelThatAlsoFailsToDeleteStub
{
    public function save()
    {
        return true;
    }

    public function delete()
    {
        throw new Exception('OH NOES!');
    }
}

#[\AllowDynamicProperties]
class ModelWithNoSaveMethodStub
{
    public function delete()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class ModelWithNoDeleteMethodStub
{
    public function save()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class ModelWithValidationErrorsStub
{
    public $validationErrors = 'Failed to save!';

    public function save()
    {
        //
    }

    public function delete()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class ModelWithBadValidationErrorsStub
{
    public $validationErrors = 'Oh noes';

    public function save()
    {
        //
    }

    public function delete()
    {
        return true;
    }
}

#[\AllowDynamicProperties]
class ModelWithTrackedSaves
{
    public $saveCounter = 0;

    public function save()
    {
        $this->saveCounter++;

        return true;
    }

    public function delete()
    {
        return true;
    }
}
