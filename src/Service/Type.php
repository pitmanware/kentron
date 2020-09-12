<?php

namespace Kentron\Service;

final class Type
{
    public const TYPE_ARRAY = "array";
    public const TYPE_BOOLEAN = "boolean";
    public const TYPE_DOUBLE = "double";
    public const TYPE_FLOAT = "double";
    public const TYPE_INTEGER = "integer";
    public const TYPE_OBJECT = "object";
    public const TYPE_RESOURCE = "resource";
    public const TYPE_STRING = "string";

    /**
     * Checks if an array is associative or numeric indexed
     *
     * @param array $array The array to check
     *
     * @return bool True is the array is associative
     */
    public static function isAssoc (array $array): bool
    {
        return !!array_filter(array_keys($array), 'is_string');
    }

    /**
     * Checks if a variable can be used in a foreach
     * Also works with callables
     *
     * @param mixed $data The variable to check
     *
     * @return bool True if the variable can be traversed
     */
    public static function isIterable ($data): bool
    {
        return is_array($data) || is_object($data);
    }

    /**
     * Returns a property from an iterable scalar
     *
     * @param array|object $data     The data to extract a value from
     * @param string       $property The key
     *
     * @return mixed Null if no key exists
     */
    public static function getProperty ($data, string $property)
    {
        if (is_object($data) && property_exists($data, $property))
        {
            return $data->{$property};
        }
        else if (is_array($data) && isset($data[$property]))
        {
            return $data[$property];
        }

        return null;
    }

    /**
     * Gets one of the casting methods
     *
     * @param string $type The type to cast to
     *
     * @return string The method name
     *
     * @throws \UnexpectedValueException If the given type is unknown
     */
    public static function castTo (string $type): string
    {
        switch (strtolower($type))
        {
            case static::TYPE_ARRAY:
                return "toArray";
                break;

            case static::TYPE_BOOLEAN:
                return "toBool";
                break;

            case static::TYPE_FLOAT:
            case static::TYPE_DOUBLE:
                return "toFloat";
                break;

            case static::TYPE_INTEGER:
                return "toInt";
                break;

            case static::TYPE_OBJECT:
                return "toObject";
                break;

            case static::TYPE_STRING:
                return "toString";
                break;

            default:
                throw new \UnexpectedValueException("$type is not a valid type");
                break;
        }
    }

    /**
     * Casts to an array
     *
     * @param mixed $value Accepts any type
     *
     * @return array
     */
    public static function toArray ($value): array
    {
        return (array) $value;
    }

    /**
     * Casts to a boolean
     *
     * @param mixed $value Accepts any type
     *
     * @return bool
     */
    public static function toBool ($value): bool
    {
        return (bool) $value;
    }

    /**
     * Casts to a float
     *
     * @param mixed $value Accepts anything but an object
     *
     * @return float
     *
     * @throws InvalidArgumentException
     */
    public static function toFloat ($value): float
    {
        if (is_object($value))
        {
            throw new \InvalidArgumentException("Non object expected");
        }

        return (float) $value;
    }

    /**
     * Casts to an integer
     *
     * @param mixed $value Accepts anything but an object
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    public static function toInt ($value): int
    {
        if (is_object($value))
        {
            throw new \InvalidArgumentException("Non object expected");
        }

        return (int) $value;
    }

    /**
     * Casts to an object
     *
     * @param mixed $value Accepts any type
     *
     * @return object
     */
    public static function toObject ($value): object
    {
        return (object) $value;
    }

    /**
     * Casts to a string
     *
     * @param mixed $value Accepts anything except iterable or object
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public static function toString ($value): string
    {
        if (is_iterable($value) || is_object($value))
        {
            throw new \InvalidArgumentException("Non iterable/object expected");
        }

        return (string) $value;
    }
}
