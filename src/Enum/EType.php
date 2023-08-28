<?php
declare(strict_types=1);

namespace Kentron\Enum;

use Throwable;

enum EType: string
{
    case TYPE_ARRAY = "array";
    case TYPE_BOOL = "bool";
    case TYPE_BOOLEAN = "boolean";
    case TYPE_DOUBLE = "double";
    case TYPE_FLOAT = "float";
    case TYPE_INT = "int";
    case TYPE_INTEGER = "integer";
    case TYPE_NULL = "null";
    case TYPE_OBJECT = "object";
    case TYPE_RESOURCE = "resource";
    case TYPE_STRING = "string";
    case TYPE_DT = "dt";
    case TYPE_JSON = "json";

    case TYPE_ARRAY_ARRAY = "array_array";
    case TYPE_ARRAY_BOOL = "array_bool";
    case TYPE_ARRAY_BOOLEAN = "array_boolean";
    case TYPE_ARRAY_DOUBLE = "array_double";
    case TYPE_ARRAY_FLOAT = "array_float";
    case TYPE_ARRAY_INT = "array_int";
    case TYPE_ARRAY_INTEGER = "array_integer";
    case TYPE_ARRAY_OBJECT = "array_object";
    case TYPE_ARRAY_STRING = "array_string";
    case TYPE_ARRAY_DT = "array_dt";

    /**
     * Checks if a type is available to be coerced
     *
     * @param string $type The type to cast to
     *
     * @return bool If the type is available
     */
    public static function exists(string $type): bool
    {
        return !!self::tryFrom(strtolower($type));
    }

    public static function fromType(mixed $value): static
    {
        $type = self::from(gettype($value));

        $type = match($type) {
            self::TYPE_BOOLEAN => self::TYPE_BOOL,
            self::TYPE_DOUBLE => self::TYPE_FLOAT,
            self::TYPE_INTEGER => self::TYPE_INT,
            default => $type
        };

        return $type;
    }

    public static function tryFromType(mixed $value): ?static
    {
        try {
            return self::fromType($value);
        }
        catch (Throwable) {
            return null;
        }
    }
}
