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

namespace League\FactoryMuffin;

/**
 * This is the model definition class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
final class Definition
{
    /**
     * The model class name.
     *
     * @var string
     */
    private string $class;

    /**
     * The model group.
     *
     * @var string|null
     */
    private ?string $group;

    /**
     * The maker.
     *
     * @var callable|null
     */
    private $maker;

    /**
     * The callback.
     *
     * @var callable|null
     */
    private $callback;

    /**
     * The attribute definitions.
     *
     * @var array
     */
    private array $definitions = [];

    /**
     * Create a new model definition instance.
     *
     * @param string $class The model class name.
     *
     * @return void
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * Returns the real model class without the group prefix.
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Set the model group.
     *
     * @param string|null $group The model group.
     *
     * @return Definition
     */
    public function setGroup(?string $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get the model group.
     *
     * @return string|null
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     * Set the maker.
     *
     * @param callable $maker The maker.
     *
     * @return Definition
     */
    public function setMaker(callable $maker): self
    {
        $this->maker = $maker;

        return $this;
    }

    /**
     * Clear the maker.
     *
     * @return Definition
     */
    public function clearMaker()
    {
        $this->maker = null;

        return $this;
    }

    /**
     * Get the maker.
     *
     * @return callable|null
     */
    public function getMaker(): ?callable
    {
        return $this->maker;
    }

    /**
     * Set the callback.
     *
     * @param callable $callback The callback.
     *
     * @return Definition
     */
    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Clear the callback.
     *
     * @return Definition
     */
    public function clearCallback(): self
    {
        $this->callback = null;

        return $this;
    }

    /**
     * Get the callback.
     *
     * @return callable|null
     */
    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    /**
     * Add an attribute definitions.
     *
     * Note that we're appending to the original attribute definitions here.
     *
     * @param string $attribute  The attribute name.
     * @param callable|string $definition The attribute definition.
     *
     * @return Definition
     */
    public function addDefinition(string $attribute, callable|string $definition): self
    {
        $this->definitions[$attribute] = $definition;

        return $this;
    }

    /**
     * Set the attribute definitions.
     *
     * Note that we're appending to the original attribute definitions here
     * instead of switching them out for the new ones.
     *
     * @param array $definitions The attribute definitions.
     *
     * @return Definition
     */
    public function setDefinitions(array $definitions = []): self
    {
        $this->definitions = array_merge($this->definitions, $definitions);

        return $this;
    }

    /**
     * Clear the attribute definitions.
     *
     * @return Definition
     */
    public function clearDefinitions(): self
    {
        $this->definitions = [];

        return $this;
    }

    /**
     * Get the attribute definitions.
     *
     * @return array
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }
}
