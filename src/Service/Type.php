<?php

namespace Kentron\Service;

use Kentron\Facade\DT;

final class Type
{
    public const TYPE_ARRAY = "array";
    public const TYPE_BOOLEAN = "boolean";
    public const TYPE_DOUBLE = "double";
    public const TYPE_FLOAT = "float";
    public const TYPE_INTEGER = "integer";
    public const TYPE_OBJECT = "object";
    public const TYPE_RESOURCE = "resource";
    public const TYPE_STRING = "string";
    public const TYPE_DT = "dt";

    private static $value;
    private static $quiet = false;

    /**
     * Checks if an array is associative or numeric indexed
     *
     * @param array $array The array to check
     *
     * @return bool True is the array is associative
     */
    public static function isAssoc(array $array): bool
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
    public static function isIterable($data): bool
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
    public static function getProperty($data, string $property)
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
    * Sets value param and allows chaining
    *
    * @param mixed $value
    *
    * @return self
    */
    public static function cast($value)
    {
        self::$value = $value;
        return self::class;
    }

    /**
     * Disables throwing an exception on failure to cast
     *
     * @return mixed
     */
    public static function quietly()
    {
        self::$quiet = true;
        return self::class;
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
    public static function castTo(string $type): string
    {
        switch (strtolower($type)) {
            case static::TYPE_ARRAY:
                return "castToArray";
                break;

            case static::TYPE_BOOLEAN:
                return "castToBool";
                break;

            case static::TYPE_FLOAT:
            case static::TYPE_DOUBLE:
                return "castToFloat";
                break;

            case static::TYPE_INTEGER:
                return "castToInt";
                break;

            case static::TYPE_OBJECT:
                return "castToObject";
                break;

            case static::TYPE_STRING:
                return "castToString";
                break;

            default:
                throw new \UnexpectedValueException("$type is not a valid type");
                break;
        }
    }

    /**
    * Gets one of the casting methods
    *
    * @param string $type The type to cast to
    *
    * @return mixed The result of the method call
    *
    * @throws \UnexpectedValueException If the given type is unknown
    */
    public static function to(string $type)
    {
        switch (strtolower($type)) {
            case static::TYPE_ARRAY:
                return self::castToArray(self::$value);

            case static::TYPE_BOOLEAN:
                return self::castToBool(self::$value);

            case static::TYPE_FLOAT:
            case static::TYPE_DOUBLE:
                return self::castToFloat(self::$value);

            case static::TYPE_INTEGER:
                return self::castToInt(self::$value);

            case static::TYPE_OBJECT:
                return self::castToObject(self::$value);

            case static::TYPE_STRING:
                return self::castToString(self::$value);

            case static::TYPE_DT:
                return self::castToDT(self::$value);

            default:
                if (!self::$quiet) {
                    throw new \UnexpectedValueException("$type is not a valid type");
                }
        }
    }

    /**
     * Casts to an array
     *
     * @param mixed $value Accepts any type
     *
     * @return array
     */
    public static function castToArray($value): array
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
    public static function castToBool($value): bool
    {
        return (bool) $value;
    }

    /**
     * Casts to a float
     *
     * @param mixed $value Accepts anything but an object
     *
     * @return mixed Float or original value on quiet exception
     *
     * @throws InvalidArgumentException
     */
    public static function castToFloat($value)
    {
        if (is_object($value)) {
            if (self::$quiet) {
                return $value;
            }

            throw new \InvalidArgumentException("Non object expected");
        }

        return (float) $value;
    }

    /**
     * Casts to an integer
     *
     * @param mixed $value Accepts anything but an object
     *
     * @return mixed Int or original value on quiet exception
     *
     * @throws InvalidArgumentException
     */
    public static function castToInt($value)
    {
        if (is_object($value)) {
            if (self::$quiet) {
                return $value;
            }

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
    public static function castToObject($value): object
    {
        return (object) $value;
    }

    /**
     * Casts to a string
     *
     * @param mixed $value Accepts anything except iterable or object
     *
     * @return mixed String or original value on quiet exception
     *
     * @throws InvalidArgumentException
     */
    public static function castToString($value)
    {
        if (is_iterable($value) || is_object($value)) {
            if (self::$quiet) {
                return $value;
            }

            throw new \InvalidArgumentException("Non iterable/object expected");
        }

        return (string) $value;
    }

    /**
     * Casts to DT
     *
     * @param mixed $value Expects string or null
     *
     * @return mixed DT or original value on quiet exception
     */
    public static function castToDT($value)
    {
        if (is_string($value)) {
            return new DT($value);
        }

        if (self::$quiet) {
            return $value;
        }

        throw new \InvalidArgumentException("Could not create DT with type " . gettype($value));
    }
}
