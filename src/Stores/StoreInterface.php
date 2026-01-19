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

namespace League\FactoryMuffin\Stores;

use League\FactoryMuffin\Exceptions\DeletingFailedException;
use League\FactoryMuffin\Exceptions\SaveFailedException;

/**
 * This is the store interface.
 *
 * @author Graham Campbell <graham@alt-three.com>
 * @author Scott Robertson <scottymeuk@gmail.com>
 */
interface StoreInterface
{
    /**
     * Save the model to the database.
     *
     * @param object $model The model instance.
     *
     * @return void
     *@throws SaveFailedException
     *
     */
    public function persist(object $model): void;

    /**
     * Return an array of models waiting to be saved.
     *
     * @return object[]
     */
    public function pending(): array;

    /**
     * Mark a model as waiting to be saved.
     *
     * @param object $model The model instance.
     *
     * @return void
     */
    public function markPending(object $model): void;

    /**
     * Is the model waiting to be saved?
     *
     * @param object $model The model instance.
     *
     * @return bool
     */
    public function isPending(object $model): bool;

    /**
     * Return an array of saved models.
     *
     * @return object[]
     */
    public function saved(): array;

    /**
     * Mark a model as saved.
     *
     * @param object $model The model instance.
     *
     * @return void
     */
    public function markSaved(object $model): void;

    /**
     * Is the model saved?
     *
     * @param object $model The model instance.
     *
     * @return bool
     */
    public function isSaved(object $model): bool;

    /**
     * Delete all the saved models.
     *
     * @throws DeletingFailedException
     *
     * @return void
     */
    public function deleteSaved(): void;
}
