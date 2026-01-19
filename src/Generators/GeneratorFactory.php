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

namespace League\FactoryMuffin\Generators;

use League\FactoryMuffin\Exceptions\SaveFailedException;
use League\FactoryMuffin\FactoryMuffin;

/**
 * This is the generator factory class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 * @author Scott Robertson <scottymeuk@gmail.com>
 * @author Michael Bodnarchuk <davert@codeception.com>
 */
class GeneratorFactory
{
    /**
     * Automatically generate the attribute we want.
     *
     * @param callable|string $kind The kind of attribute.
     * @param object $model The model instance.
     * @param FactoryMuffin $factoryMuffin The factory muffin instance.
     *
     * @return mixed
     * @throws SaveFailedException
     */
    public function generate(callable|string $kind, object $model, FactoryMuffin $factoryMuffin): mixed
    {
        $generator = $this->make($kind, $model, $factoryMuffin);

        if ($generator) {
            return $generator->generate();
        }

        return $kind;
    }

    /**
     * Automatically make the generator class we need.
     *
     * @param callable|string $kind          The kind of attribute.
     * @param object $model         The model instance.
     * @param FactoryMuffin $factoryMuffin The factory muffin instance.
     *
     * @return GeneratorInterface|null
     */
    public function make(callable|string $kind, object $model, FactoryMuffin $factoryMuffin): FactoryGenerator|EntityGenerator|CallableGenerator|GeneratorInterface|null
    {
        if (is_callable($kind)) {
            return new CallableGenerator($kind, $model, $factoryMuffin);
        }

        if (is_string($kind) && str_starts_with($kind, EntityGenerator::getPrefix())) {
            return new EntityGenerator($kind, $model, $factoryMuffin);
        }

        if (is_string($kind) && str_starts_with($kind, FactoryGenerator::getPrefix())) {
            return new FactoryGenerator($kind, $model, $factoryMuffin);
        }

        return null;
    }
}
