<?php

namespace Kentron\Service;

final class Type
{
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
     * @param mixed  $data     The data to extract a value from
     * @param string $property The key
     *
     * @return mixed Null if no key exists
     */
    public static function getProperty ($data, string $property)
    {
        if (is_object($data) && property_exists($data, $property)) {
            return $data->{$property};
        }
        else if (is_array($data) && isset($data[$property])) {
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
    public static function getTypeMethod (string $type): string
    {
        switch (strtolower($type))
        {
            case "array":
                return "castToArray";
                break;
            case "bool":
            case "boolean":
                return "castToBool";
                break;
            case "float":
            case "double":
                return "castToFloat";
                break;
            case "int":
            case "integer":
                return "castToInt";
                break;
            case "object":
                return "castToObject";
                break;
            case "string":
                return "castToString";
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
    public static function castToArray ($value): array
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
    public static function castToBool ($value): bool
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
    public static function castToFloat ($value): float
    {
        if (is_object($value)) {
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
    public static function castToInt ($value): int
    {
        if (is_object($value)) {
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
    public static function castToObject ($value): object
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
    public static function castToString ($value): string
    {
        if (is_iterable($value) || is_object($value)) {
            throw new \InvalidArgumentException("Non iterable/object expected");
        }

        return (string) $value;
    }
}
