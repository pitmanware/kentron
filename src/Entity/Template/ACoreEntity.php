<?php

namespace Kentron\Entity\Template;

use Kentron\Service\Type;

abstract class ACoreEntity extends AEntity
{
    /**
     * The core entity
     *
     * @var AEntity
     */
    protected $rootEntity;

    /**
     * Allows you to put a prefix in for the property map
     *
     * @var string
     */
    protected $prefix = "";

    /**
     * Relevant property map to build the entity dynamically
     * Should be overridden
     *
     * @var array<string,array<string,mixed>>
     *
     * ### Example
     * ```
     * $propertyMap = [
     *     "property_key" => [
     *         "get" => "getClassProperty", // String of the getter method or callable
     *         "set" => "setClassProperty",
     *         "add" => "addClassProperty", // Used for setting a nested class
     *         "get_cast" => "dt", // Allows casting with \App\Core\Type on get
     *         "set_cast" => "dt", // Allows casting with \App\Core\Type on set
     *         "get_class" => "getClassInstance" // For nesting; has to be on the extended ACoreEntity
     *         "set_class" => "setClassInstance" // For nesting; has to be on the extended ACoreEntity
     *         "flatten" => bool // For extracting items out of a nested object/array into the current entity
     *     ],
     *     ...
     * ];
     * ```
     */
    protected $propertyMap = [];

    /**
     * May be overridden
     *
     * @param null|AEntity $entity Supplies the core entity or self
     */
    public function __construct(?AEntity $entity = null)
    {
        $this->rootEntity = $entity;
    }

    /**
     * Destroy the core entity on destruct
     */
    public function __destruct()
    {
        $this->rootEntity = null;
    }

    /**
     * Get "child" entity or self
     *
     * @return AEntity
     */
    public function getRootEntity(): AEntity
    {
        return $this->rootEntity ?? $this;
    }

    /**
     * Dynamically call methods on the core entity
     *
     * @param string $callable The method to call
     * @param array  $args     The arguments to pass into the method
     *
     * @return mixed
     *
     * @throws \Error On invalid method call
     */
    final public function __call(string $callable, array $args = [])
    {
        if (is_null($this->rootEntity)) {
            throw new \Error("Call to undefined method " . get_class($this) . "::{$callable}");
        }
        return $this->rootEntity->{$callable}(...$args);
    }

    /**
     * Builds the entity from the database or API
     *
     * @param array|object $data The input data
     *
     * @return void
     */
    final public function build($data): void
    {
        if (!Type::isIterable($data)) {
            return;
        }

        /** @var array<string,(string|callable|bool)> $binding */
        foreach ($this->propertyMap as $property => $binding) {

            $dataProperty = Type::getProperty($data, $property);

            if (is_null($dataProperty)) {
                continue;
            }

            /** @var bool|null */
            $flatten = $binding["flatten"] ?? null;
            if (
                (is_bool($flatten) && $flatten) &&
                (
                    (is_array($dataProperty) && Type::isAssoc($dataProperty)) ||
                    is_object($dataProperty)
                )
            ) {
                $this->build($dataProperty);
                continue;
            }

            // Look for a nested class
            /** @var string|callable|null $adder */
            $adder = $binding["add"] ?? null;

            if (!is_null($adder)) {

                if (is_array($dataProperty) && !Type::isAssoc($dataProperty)) {
                    // If the property is an indexed array and the class is a collection
                    foreach ($dataProperty as $prop) {
                        $this->callRootEntityBinding($adder, $this->buildClass($binding, $prop));
                    }
                }
                else {
                    $this->callRootEntityBinding($adder, $this->buildClass($binding, $dataProperty));
                }

                continue;
            }
            else {
                $dataProperty = $this->buildClass($binding, $dataProperty);
            }

            /** @var string|null $caster */
            if ($caster = $binding["set_cast"] ?? null) {
                $dataProperty = Type::cast($dataProperty)::quietly()::to($caster);
            }

            /** @var string|callable|null $setter */
            $setter = $binding["set"] ?? null;
            $this->callRootEntityBinding($setter, $dataProperty);
        }
    }

    /**
     * Generator for the properties
     *
     * @param boolean $allowNullable
     *
     * @return iterable
     *
     * @throws \TypeError
     */
    final public function iterateProperties(bool $allowNullable = true): iterable
    {
        /** @var array<string,(string|callable|bool)> $binding */
        foreach ($this->propertyMap as $key => $binding) {

            /** @var string|callable|null $getter */
            $getter = $binding["get"] ?? null;
            $propertyValue = $this->callRootEntityBinding($getter);

            if (is_null($propertyValue) && !$allowNullable) {
                continue;
            }

            /** @var string|null $caster */
            if ($caster = $binding["get_cast"] ?? null) {
                $propertyValue = Type::cast($propertyValue)::quietly()::to($caster);
            }

            yield ($this->prefix . $key) => $propertyValue;
        }
    }

    /**
     * The reverse of the build function returning the property map with the entity values
     *
     * @return array<string,mixed>
     */
    public function normalise(): array
    {
        $entity = $this->getRootEntity();
        if (!$this->entityIsRecursive($entity) && $entity->isValidMethod(__FUNCTION__)) {
            return $this->callRootEntityMethod(__FUNCTION__);
        }

        $normalised = [];

        foreach ($this->iterateProperties(true) as $property => $value) {
            $normalised[$property] = $value;
        }

        return $normalised;
    }

    /**
     * Gets a class using the getter and builds it is the propery is iterable
     *
     * @param array<string,(string|callable)> $binding      The binding from $propertyMap
     * @param mixed $dataProperty The property to build with
     *
     * @return mixed Either the built entity or the original property
     */
    private function buildClass(array $binding, $dataProperty)
    {
        // If the property is an object or array,
        // check to see if there is a getter for the class
        /** @var string|callable|null */
        $classGetter = $binding["get_class"] ?? null;

        if (is_null($classGetter)) {
            return $dataProperty;
        }

        /** @var AEntity|null $entity */
        $entity = $this->callBinding($classGetter);

        if (is_null($entity)) {
            return $dataProperty;
        }

        // The subclass must be another instance of ACoreEntity
        if (!$this->entityIsRecursive($entity)) {
            return $dataProperty;
        }

        /** @var ACoreEntity $entity */
        if ($this->entityIsACollection($entity)) {
            /** @var ACoreCollectionEntity $entity */
            $entity->buildCollection($dataProperty);
        }
        else {
            $entity->build($dataProperty);
        }

        /** @var string|callable|null */
        $classSetter = $binding["set_class"] ?? null;
        $this->callBinding($classSetter, $entity);

        return $entity->getRootEntity();
    }

    /**
     * Calls a valid method on the rootEntity
     *
     * @param string $method The method
     * @param mixed  $params Any parameters to be passed to the method
     *
     * @return mixed
     */
    private function callRootEntityMethod(string $method, ...$params)
    {
        return $this->getRootEntity()->{$method}(...$params);
    }

    /**
     * Checks if a method can be run on the root entity
     *
     * @param mixed $method
     *
     * @return boolean
     */
    private function isValidRootEntityMethod($method = null): bool
    {
        if (!is_string($method)) {
            return false;
        }

        return $this->getRootEntity()->isValidMethod($method);
    }

    /**
     * Call a method on the root entity or closure
     *
     * @param mixed $method
     * @param mixed $value
     *
     * @return mixed
     */
    private function callRootEntityBinding($method, $value = null)
    {
        if ($this->isValidRootEntityMethod($method)) {
            return $this->callRootEntityMethod($method, $value);
        }
        else if (is_callable($method)){
            return $method($value);
        }

        return null;
    }

    /**
     * Call a method on this or closure
     *
     * @param mixed $method
     * @param mixed $value
     *
     * @return mixed
     */
    private function callBinding($method, $value = null)
    {
        if ($this->isValidMethod($method)) {
            return $this->{$method}($value);
        }
        else if (is_callable($method)){
            return $method($value);
        }

        return null;
    }

    /**
     * Allows for recursive building if entity extends this
     *
     * @param AEntity $entity The entity to check
     *
     * @return bool
     */
    private function entityIsRecursive(AEntity $entity): bool
    {
        return is_subclass_of($entity, __CLASS__);
    }

    /**
     * Checks if the given entity extends from ACoreCollectionEntity
     *
     * @param AEntity $entity The entity to check
     *
     * @return bool
     */
    private function entityIsACollection(AEntity $entity): bool
    {
        return is_subclass_of($entity, ACoreCollectionEntity::class);
    }
}
