<?php

namespace Kentron\Entity\Template;

use Kentron\Service\Type;

abstract class ACoreEntity extends AEntity
{
    /**
     * The core entity
     * @var AEntity
     */
    protected $rootEntity;

    /**
    * Relevant property map to build the entity dynamically
    * Should be overridden
    * @var array
    * @example [
    *     "property_key" => [
    *         "get" => "getClassProperty", // Get, set and add are on the core entity
    *         "set" => "setClassProperty",
    *         "add" => "addClassProperty", // Used for setting a nested class
    *         "get_class" => "getClassInstance" // For nesting; has to be on the extended ACoreEntity
    *         "set_class" => "setClassInstance" // For nesting; has to be on the extended ACoreEntity
    *         "flatten" => bool // For extracting items out of a nested object/array into the current entity
    *     ]
    * ]
    */
    protected $propertyMap = [];

    /**
     * Constructor can only be called by child
     * Should be overridden
     * @param AEntity $entity Supplies the core entity
     */
    protected function __construct (AEntity $entity)
    {
        $this->rootEntity = $entity;
    }

    /**
     * Destroy the core entity on destruct
     */
    public function __destruct ()
    {
        $this->rootEntity = null;
    }

    /**
     * Dynamically call methods on the core entity
     * @param  string $callable The method to call
     * @param  array  $args     The arguments to pass into the method
     * @return mixed
     * @throws Error On invalid method call
     */
    final public function __call (string $callable, array $args = [])
    {
        return $this->rootEntity->{$callable}(...$args);
    }

    /**
     * Alternatively the core entity can be returned
     * @return AEntity
     */
    final public function getRootEntity (): AEntity
    {
        return $this->rootEntity;
    }

    /**
     * Builds the entity from the database or API
     * @param  array|object $data The input data
     * @return void
     */
    final public function build ($data): void
    {
        if (!Type::isIterable($data)) {
            return;
        }

        foreach ($this->propertyMap as $property => $binding) {

            $dataProperty = Type::getProperty($data, $property);

            if (is_null($dataProperty)) {
                continue;
            }

            if (
                ($binding["flatten"] ?? false) &&
                (
                    (is_array($dataProperty) && Type::isAssoc($dataProperty)) ||
                    is_object($dataProperty)
                )
            ) {
                $this->build($dataProperty);
                continue;
            }

            // Look for a nested class
            $adder = $binding["add"] ?? null;

            if ($this->isValidMethod($this->rootEntity, $adder)) {

                if (is_array($dataProperty) && !Type::isAssoc($dataProperty)) {
                    // If the property is an indexed array and the class is a collection
                    foreach ($dataProperty as $prop) {
                        $this->callRootEntityMethod($adder, $this->buildClass($binding, $prop));
                    }
                }
                else {
                    $this->callRootEntityMethod($adder, $this->buildClass($binding, $dataProperty));
                }

                continue;
            }
            else {
                $dataProperty = $this->buildClass($binding, $dataProperty);
            }

            $this->callRootEntityMethod($binding["set"] ?? null, $dataProperty);
        }
    }

    /**
     * Generator for the properties
     * @return iterable
     * @throws \TypeError
     */
    final public function iterateProperties (bool $allowNullable = false): iterable
    {
        foreach ($this->propertyMap as $key => $binding) {
            $getter = $binding["get"] ?? null;

            if (!$this->isValidMethod($this->rootEntity, $getter)) {
                continue;
            }

            try {
                $propertyValue = $this->rootEntity->{$getter}();
            }
            catch (\TypeError $typeError) {
                if (!$allowNullable) {
                    throw $typeError;
                }
                $propertyValue = null;
            }

            yield $key => $propertyValue;
        }
    }

    /**
     * The reverse of the build function returning the property map with the entity values
     * @return array
     */
    public function normalise (): array
    {
        if ($this->isValidMethod($this->rootEntity, __FUNCTION__)) {
            return $this->callRootEntityMethod(__FUNCTION__);
        }

        $normalised = [];

        foreach ($this->iterateProperties(true) as $property => $value) {
            $normalised[$property] = $value;
        }

        return $normalised;
    }

    /**
     * Validates a callable method on the core entity
     * @param  string|null $method The method to check
     * @return bool
     */
    final protected function isValidMethod (AEntity $entity, ?string $method = null): bool
    {
        if (
            isset($method) &&
            method_exists($entity, $method) &&
            is_callable([$entity, $method])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Gets a class using the getter and builds it is the propery is iterable
     * @param  array  $binding      The binding from $propertyMap
     * @param  mixed  $dataProperty The property to build with
     * @return mixed                Either the built entity or the original property
     */
    final private function buildClass (array $binding, $dataProperty)
    {
        // If the property is an object or array,
        // check to see if there is a getter for the class
        $classGetter = $binding["get_class"] ?? null;

        if ($this->isValidMethod($this, $classGetter)) {
            $entity = $this->{$classGetter}();

            // The subclass must be another instance of ACoreEntity
            if ($this->entityIsRecursive($entity)) {

                if ($this->entityIsACollection($entity)) {
                    $entity->buildCollection($dataProperty);
                }
                else {
                    $entity->build($dataProperty);
                }
            }

            $classSetter = $binding["set_class"] ?? null;

            if ($this->isValidMethod($this, $classSetter)) {
                $this->{$classSetter}($entity);
            }

            return $entity->getRootEntity();
        }

        return $dataProperty;
    }

    /**
     * Calls a valid method on the rootEntity
     * @param string $method The method
     * @param mixed  $params Any parameters to be passed to the method
     * @return mixed
     */
    final private function callRootEntityMethod (?string $method, ...$params)
    {
        if ($this->isValidMethod($this->rootEntity, $method)) {
            return $this->rootEntity->{$method}(...$params);
        }

        return null;
    }

    /**
     * Allows for recursive building if entity extends this
     * @param  AEntity $entity The entity to check
     * @return bool
     */
    final private function entityIsRecursive (AEntity $entity): bool
    {
        if (is_subclass_of($entity, __CLASS__)) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the given entity extends from ACoreCollectionEntity
     * @param  AEntity $entity The entity to check
     * @return bool
     */
    final private function entityIsACollection (AEntity $entity): bool
    {
        if (is_subclass_of($entity, ACoreCollectionEntity::class)) {
            return true;
        }

        return false;
    }
}
