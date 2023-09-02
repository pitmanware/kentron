<?php
declare(strict_types=1);

namespace Kentron\Template\Entity;

// Services
use Kentron\Support\Type\Type;
use Kentron\Support\Json;

// Enums
use Kentron\Enum\EType;

// Entities
use Kentron\Template\Entity\AEntity;

// Throwable
use \Error;
use \TypeError;

abstract class ACoreEntity extends AEntity
{
    /** Allows you to put a prefix in for the property map */
    protected string $prefix = "";

    /**
     * Relevant property map to hydrate the entity dynamically
     * Should be overridden
     *
     * @var array<string,array<string,mixed>>|array<string,string>
     *
     * ### Named Example
     * ```
     * $propertyMap = [
     *     "property_key" => [
     *         "get" => "getClassProperty", // String of the getter method, array of getters or callable
     *         "set" => "setClassProperty",
     *         "add" => "addClassProperty", // Used for setting a nested class
     *         "prop" => "classProperty", // Raw property assignment, can only be used with statically typed variables PHP 7.4+
     *         "get_prop" => classProperty or ["classProperty", ...], // Raw property getter, can only be used with statically typed variables PHP 7.4+
     *         "get_cast" => "dt", // Allows casting with Kentron\Support\Type\Type or callable method on get
     *         "set_cast" => "dt", // Allows casting with Kentron\Support\Type\Type or callable method on set (Can be string[])
     *         "get_class" => "getClassInstance" // Method for nesting; has to be on the extended ACoreEntity <-- Only used on setting
     *         "set_class" => "setClassInstance" // Method for nesting; has to be on the extended ACoreEntity <-^ (<- mostly redundant cause objects are passed by reference)
     *         "flatten" => bool // For extracting items out of a nested object/array into the current entity
     *         "prop_as_name" => bool // Use the prop name as the extracted name when normalising and casting. Must be used with "prop"
     *     ],
     *     ...
     * ];
     * ```
     * ### 1D Example
     * ```
     * $propertyMap = [
     *     "property_key" => "classProperty", // Raw property assignment, can only be used with statically typed variables PHP 7.4+
     *     ...
     * ];
     * ```
     */
    protected array $propertyMap = [];

    /**
     * Builds the entity from the database or API
     *
     * @param array|object $data The input data
     *
     * @return void
     */
    public function hydrate($data, bool $allowNull = false): void
    {
        if (!Type::isIterable($data)) {
            return;
        }

        /**
         * @var string|int $property
         * @var string|array<string, (string|callable|bool)> $binding
         */
        foreach ($this->propertyMap as $property => $binding) {

            if (is_int($property)) {
                throw new Error("Property map for " . $this::class . " cannot be a 1D array");
            }

            $dataProperty = Type::getProperty($data, $property);

            if (!$allowNull && is_null($dataProperty)) {
                continue;
            }

            if (is_string($binding)) {
                $this->bindSetProperty($binding, $dataProperty);
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
                $this->hydrate($dataProperty, $allowNull);
                continue;
            }

            // Look for a nested class
            /** @var string|callable|null $adder */
            $adder = $binding["add"] ?? null;

            if (!is_null($adder)) {

                if (is_array($dataProperty) && !Type::isAssoc($dataProperty)) {
                    // If the property is an indexed array and the class is a collection
                    foreach ($dataProperty as $prop) {
                        $this->callBinding($adder, $this->hydrateClass($binding, $prop, $allowNull));
                    }
                }
                else {
                    $this->callBinding($adder, $this->hydrateClass($binding, $dataProperty, $allowNull));
                }

                continue;
            }
            else {
                $dataProperty = $this->hydrateClass($binding, $dataProperty, $allowNull);
            }

            /** @var string|string[]|null $caster */
            $caster = $binding["set_cast"] ?? null;
            if (!($dataProperty instanceof self)) {
                // If we want to use the property name instead of the key for casting
                if (($binding["prop_as_name"] ?? false) && ($binding["prop"] ?? false) && is_string($binding["prop"])) {
                    $property = $binding["prop"];
                }

                // Accepts an array of methods/Type::casts to cast with
                if (Type::of($caster)->isArrayOf(EType::String)) {
                    foreach ($caster as $cast) {
                        if ($this->isValidMethod($cast)) {
                            $dataProperty = $this->{$cast}($dataProperty, $property);
                        }
                        else {
                            $dataProperty = Type::cast($dataProperty)->quietly()->to(EType::from($cast));
                        }
                    }
                }
                else if (is_string($caster)) {
                    if ($this->isValidMethod($caster)) {
                        $dataProperty = $this->{$caster}($dataProperty, $property);
                    }
                    else {
                        $dataProperty = Type::cast($dataProperty)->quietly()->to(EType::from($caster));
                    }
                }
            }

            /** @var string|callable|null $setter */
            $setter = $binding["set"] ?? null;

            if (is_string($setter)) {
                $this->callBinding($setter, $dataProperty);
            }
            else {
                $setProp = $binding["prop"] ?? null;
                if (is_string($setProp)) {
                    $this->bindSetProperty($setProp, $dataProperty);
                }
            }
        }
    }

    /**
     * Generator for the properties
     *
     * @param boolean $allowNullable
     *
     * @return iterable
     *
     * @throws TypeError
     */
    public function iterateProperties(bool $allowNullable = true): iterable
    {
        /**
         * @var string|int $key
         * @var string|array<string, (string|callable|bool)> $binding
         */
        foreach ($this->propertyMap as $key => $binding) {

            if (is_int($key)) {
                throw new Error("Property map for " . $this::class . " cannot be a 1D array");
            }

            if (is_string($binding)) {
                $propertyValue = $this->bindGetProperty($binding);

                if (is_null($propertyValue) && !$allowNullable) {
                    continue;
                }
            }
            else {
                /** @var string|callable|null $getter */
                $getter = $binding["get"] ?? null;
                $propertyValue = null;

                if (is_string($getter) || is_array($getter)) {
                    try {
                        $propertyValue = $this->callBinding($getter);
                    }
                    catch (TypeError $typeError) {
                        if (!$allowNullable) {
                            throw $typeError;
                        }
                    }
                }
                else {
                    /** @var string|string[]|null */
                    $getProp = $binding["get_prop"] ?? null;
                    if (Type::of($getProp)->isArrayOf(EType::String)) {
                        $entity = $this;
                        foreach ($getProp as $property) {
                            $propertyValue = $entity->bindGetProperty($property);

                            if ($propertyValue instanceof AEntity) {
                                $entity = $propertyValue;
                            }
                        }
                    }
                    else {
                        $getProp ??= ($binding["prop"] ?? null);
                        if (is_string($getProp)) {
                            $propertyValue = $this->bindGetProperty($getProp);

                            if ($binding["prop_as_name"] ?? false) {
                                $key = $getProp;
                            }
                        }
                    }
                }

                if (is_null($propertyValue) && !$allowNullable) {
                    continue;
                }

                /** @var string|null $caster */
                if ($caster = $binding["get_cast"] ?? null) {
                    if ($this->isValidMethod($caster)) {
                        $propertyValue = $this->{$caster}($propertyValue);
                    }
                    else {
                        $propertyValue = Type::cast($propertyValue)->quietly()->to(EType::from($caster));
                    }
                }
            }

            yield ($this->prefix . $key) => $propertyValue;
        }
    }

    /**
     * The reverse of the hydrate function returning the property map with the entity values
     *
     * @param bool $allowNullable If true, returns null values, otherwise, they are skipped
     *
     * @return array<string,mixed>
     */
    public function normalise(bool $allowNullable = true): array
    {
        $normalised = [];

        foreach ($this->iterateProperties($allowNullable) as $property => $value) {
            $normalised[$property] = $value;
        }

        return $normalised;
    }

    /**
     * Json encode the normalised array
     *
     * @param boolean $allowNullable
     *
     * @return string
     */
    public function toJson(bool $allowNullable = true): string
    {
        return Json::toString($this->normalise($allowNullable));
    }

    /**
     * Hydrate from a json encoded string
     *
     * @param string $json
     * @param boolean $allowNull
     *
     * @return void
     */
    public function fromJson(string $json, bool $allowNull = false): void
    {
        $this->hydrate(Json::extract($json), $allowNull);
    }

    /**
     * Gets a class using the getter and hydrates it is the propery is iterable
     *
     * @param array<string, (string|callable )> $binding The binding from $propertyMap
     * @param mixed $dataProperty The property to hydrate with
     * @param bool $allowNull
     *
     * @return self|mixed Either the built entity or the original property
     */
    private function hydrateClass(array $binding, $dataProperty, bool $allowNull): mixed
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

        // The subclass must be another instance of ACoreEntity to be recursive
        if (is_null($entity) || !$this->entityIsRecursive($entity)) {
            return $dataProperty;
        }

        /** @var ACoreEntity $entity */
        if ($this->entityIsACollection($entity)) {
            /** @var ACoreCollectionEntity $entity */
            $entity->hydrateCollection($dataProperty);
        }
        else {
            $entity->hydrate($dataProperty, $allowNull);
        }

        /** @var string|callable|null */
        $classSetter = $binding["set_class"] ?? null;
        $this->callBinding($classSetter, $entity);

        return $entity;
    }

    /**
     * Calls a valid method on this
     *
     * @param string $method The method
     * @param mixed  $params Any parameters to be passed to the method
     *
     * @throws Error If the method does not exist
     *
     * @return mixed
     */
    private function callMethod(string $method, ...$params): mixed
    {
        return $this->{$method}(...$params);
    }

    /**
     * Call a method on this or closure
     *
     * @param string[]|string|callable|null $methods
     * @param mixed $value
     *
     * @return mixed
     */
    private function callBinding(mixed $methods, mixed $value = null): mixed
    {
        // Getter only
        if (is_array($methods)) {
            $entity = $this;
            foreach ($methods as $method) if ($entity->isValidMethod($method)) {
                $result = $entity->{$method}();

                if ($result instanceof AEntity) {
                    $entity = $result;
                }
            }

            return $result ?? null;
        }
        else if ($this->isValidMethod($methods)) {
            return $this->callMethod($methods, $value);
        }
        else if (is_callable($methods)) {
            return $methods($value);
        }

        return null;
    }

    /**
     * Allows for recursive hydrateing if entity extends this
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
