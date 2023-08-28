<?php
declare(strict_types=1);

namespace Kentron\Template\Entity;

use Kentron\Template\Alert\AAlert;

use Kentron\Struct\SType;
use Kentron\Support\Assert;
use Kentron\Support\Json;
use Kentron\Support\Type\Type;
use Kentron\Facade\DT;

use \Error;
use \ReflectionClass;
use \ReflectionException;
use \ReflectionNamedType;
use \ReflectionObject;
use \ReflectionProperty;
use \ReflectionUnionType;
use \UnitEnum;
use \BackedEnum;

abstract class AEntity extends AAlert
{
    public function __construct(?self $entity = null)
    {
        if (!is_null($entity)) {
            $entity->translate($this);
        }
    }

    /**
     * Validates a callable method
     *
     * @param string|null $method The method to check
     *
     * @return bool
     */
    final public function isValidMethod(?string $method = null): bool
    {
        if (
            isset($method) &&
            method_exists($this, $method) &&
            is_callable([$this, $method])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Validates a public property
     *
     * @param string|null $property The method to check
     *
     * @return bool
     */
    final public function isValidProperty(?string $property = null): bool
    {
        $rObject = new ReflectionObject($this);

        if (
            isset($property) &&
            $rObject->hasProperty($property) &&
            $rObject->getProperty($property)->isPublic() &&
            $rObject->getProperty($property)->isInitialized($this)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Translate the values of properties from one AEntity to another with the same properties
     *
     * @param self $entity The target entity
     * @param bool $clobber Set to true to replace any initialised variables on the target entity
     *
     * @return self The translated entity
     *
     * @throws Error If it could not create a reflection class for either $this or $entity
     */
    public function translate(self $entity, bool $clobber = true): self
    {
        $fromClass = $this->getReflectionClass();
        $toClass = $this->getReflectionClass($entity);

        if (is_null($fromClass) || is_null($toClass)) {
            throw new Error("Could not create reflection class for " . is_null($fromClass) ? $this::class : $entity::class);
        }

        foreach ($fromClass->getProperties(ReflectionProperty::IS_PUBLIC) as $fromProperty) {
            // If the property isn't initialised, or the target doesn't have this property by name
            if (!$fromProperty->isInitialized($this) || !$toClass->hasProperty($fromProperty->getName())) {
                continue;
            }

            $toProperty = $toClass->getProperty($fromProperty->getName());

            if (
                !$fromProperty->hasType() || !$toProperty->hasType() || // Both properties must have a type, either static or doc-ed
                !$toProperty->isPublic() || // Property to be set must be public
                ($toProperty->isInitialized($entity) && !$clobber) // If clobber is turned on, it will replace any initialised property
            ) {
                continue;
            }

            $fromValue = $fromProperty->getValue($this);

            // The values are already the same, no need to set it
            if ($toProperty->isInitialized($entity) && ($fromValue === $toProperty->getValue($entity))) {
                continue;
            }

            $toType = $toProperty->getType();

            // If the property to be set is null, but the target property type does not allow null values
            if (is_null($fromValue) && !$toType->allowsNull()) {
                continue;
            }

            $fromType = $fromProperty->getType();

            // Check if they have the same type declaration
            if ($fromType::class !== $toType::class) {
                continue;
            }

            // If the property has one type
            if (($fromType instanceof ReflectionNamedType) && ($toType instanceof ReflectionNamedType)) {
                // But they are not the same
                if ($fromType->getName() !== $toType->getName()) {
                    continue;
                }
            }
            else if (($fromType instanceof ReflectionUnionType) && ($toType instanceof ReflectionUnionType)) {
                foreach ($fromType->getTypes() as $fromPartialType) {
                    foreach ($toType->getTypes() as $toPartialType) {
                        // If the target contains this type, continue to the next type
                        if ($fromPartialType->getName() === $toPartialType->getName()) {
                            continue 2;
                        }
                    }
                    // If we get to the end of all target property types and have not found a match, they must not be the same
                    continue 2;
                }
            }

            // We should be safe to set the value
            $toProperty->setValue($entity, $fromValue);
        }

        return $entity;
    }

    // Protected methods

    /**
     * Get a property using reflection
     *
     * @param string $property
     *
     * @return mixed
     */
    protected function bindGetProperty(string $property): mixed
    {
        $reProperty = $this->getReflectionProperty($property);
        if (!is_null($reProperty) && $reProperty->isInitialized($this)) {
            return $reProperty->getValue($this);
        }
        return null;
    }

    /**
     * Set a property using reflection
     *
     * @param string $property
     * @param mixed $value
     *
     * @return void
     */
    protected function bindSetProperty(string $property, mixed $value): void
    {
        $reProperty = $this->getReflectionProperty($property);

        if (
            is_null($reProperty) ||
            (is_null($value) && $reProperty->hasType() && !$reProperty->getType()->allowsNull())
        ) {
            return;
        }

        if (!is_null($value) && $reProperty->hasType()) {

            $type = $reProperty->getType();
            /** @var ReflectionNamedType[] */
            $types = [];

            // If the property has one type
            if ($type instanceof ReflectionNamedType) {
                $types = [$type];
            }
            else if ($type instanceof ReflectionUnionType) {
                $types = $type->getTypes();
            }

            foreach ($types as $type) {
                $type = $type->getName();

                // Casts string/integer timestamps to the DT class
                if (Assert::same($type, DT::class) && (Type::isString($value) || Type::isInt($value))) {
                    $value = Type::cast($value)->quietly()->toDT();
                    break;
                }
                // Casts integer booleans (1/0) to bool
                else if (Type::isInt($value, true) && Assert::same($type, SType::TYPE_BOOL)) {
                    $value = Type::cast($value)->quietly()->toBool();
                    break;
                }
                // If the type is an enumeration, try to convert the string to it
                else if (enum_exists($type)) {
                    /** @var UnitEnum|BackedEnum $type */
                    $enumValue = null;

                    // Check if the value is a backed enum
                    if (is_a($type, BackedEnum::class, true)) {
                        $enumValue = $type::tryFrom($value);
                    }
                    if (is_null($enumValue) && is_a($type, UnitEnum::class, true)) {
                        $enumValue = constant("{$type}::{$value}");
                    }

                    if (is_null($enumValue)) {
                        throw new Error("Enum {$type}::{$value} does not exist");
                    }

                    $value = $enumValue;
                    break;
                }
                // Used for JSON strings
                else if (Type::isString($value)) {
                    if (Assert::same($type, SType::TYPE_ARRAY) && is_array($jsonArray = Json::toArray($value))) {
                        $value = $jsonArray;
                        break;
                    }
                    else if (Assert::same($type, SType::TYPE_OBJECT) && is_object($jsonObject = Json::toObject($value))) {
                        $value = $jsonObject;
                        break;
                    }
                }
            }
        }

        $reProperty->setValue($this, $value);
    }

    /**
     * Get the reflection property on a given entity or $this on null
     *
     * @param string $property
     * @param self|null $entity Or $this on null
     *
     * @return ReflectionProperty|null
     */
    protected function getReflectionProperty(string $property, ?self $entity = null): ?ReflectionProperty
    {
        try {
            return $this->getReflectionClass($entity)->getProperty($property);
        }
        catch (ReflectionException) {}

        return null;
    }

    /**
     * Get the reflection class for a given entity or $this on null
     *
     * @param self|null $entity Or $this
     *
     * @return ReflectionClass|null
     */
    protected function getReflectionClass(?self $entity = null): ?ReflectionClass
    {
        try {
            return new ReflectionClass($entity ?? $this);
        }
        catch (ReflectionException) {}

        return null;
    }
}
