<?php
declare(strict_types=1);

namespace Kentron\Struct;

final class SType
{
    public const TYPE_ARRAY = "array";
    public const TYPE_BOOL = "bool";
    public const TYPE_BOOLEAN = "boolean";
    public const TYPE_DOUBLE = "double";
    public const TYPE_FLOAT = "float";
    public const TYPE_INT = "int";
    public const TYPE_INTEGER = "integer";
    public const TYPE_OBJECT = "object";
    public const TYPE_RESOURCE = "resource";
    public const TYPE_STRING = "string";
    public const TYPE_DT = "dt";
    public const TYPE_JSON = "json";

    public const TYPE_ARRAY_ARRAY = "array_array";
    public const TYPE_ARRAY_BOOL = "array_bool";
    public const TYPE_ARRAY_BOOLEAN = "array_boolean";
    public const TYPE_ARRAY_DOUBLE = "array_double";
    public const TYPE_ARRAY_FLOAT = "array_float";
    public const TYPE_ARRAY_INT = "array_int";
    public const TYPE_ARRAY_INTEGER = "array_integer";
    public const TYPE_ARRAY_OBJECT = "array_object";
    public const TYPE_ARRAY_STRING = "array_string";
    public const TYPE_ARRAY_DT = "array_dt";

    /**
     * Checks if a type is available to be coerced
     *
     * @param string $type The type to cast to
     *
     * @return bool If the type is available
     */
    public static function exists(string $type): bool
    {
        return match (strtolower($type)) {
            self::TYPE_ARRAY,
            self::TYPE_BOOL,
            self::TYPE_BOOLEAN,
            self::TYPE_FLOAT,
            self::TYPE_DOUBLE,
            self::TYPE_INT,
            self::TYPE_INTEGER,
            self::TYPE_OBJECT,
            self::TYPE_STRING,
            self::TYPE_DT,
            self::TYPE_JSON => true,
            default => false
        };
    }
}
