<?php
declare(strict_types=1);

namespace Kentron\Enum;

use Kentron\Facade\DT;
use Kentron\Support\Json;
use Kentron\Support\Type\Type;

use \ValueError;

enum EType: string
{
    case Null = "null";
    case Array = "array";
    case Bool = "bool";
    case Boolean = "boolean";
    case Double = "double";
    case Float = "float";
    case Int = "int";
    case Integer = "integer";
    case Object = "object";
    case Resource = "resource";
    case ResourceClosed = "resource (closed)";
    case String = "string";
    case Dt = "dt";
    case Json = "json";
    case Mixed = "mixed";

    case ArrayNull = "null[]";
    case ArrayArray = "array[]";
    case ArrayBool = "bool[]";
    case ArrayFloat = "float[]";
    case ArrayInt = "int[]";
    case ArrayResource = "resource[]";
    case ArrayResourceClosed = "resource_closed[]";
    case ArrayObject = "object[]";
    case ArrayString = "string[]";
    case ArrayDt = "dt[]";
    case ArrayJson = "json[]";
    case ArrayMixed = "mixed[]";

    /**
     * Wrapper for from() but renames Boolean to Bool, Double to Float and Integer to Int
     *
     * @param string $type
     *
     * @return static
     *
     * @throws ValueError if the type is not recognised
     */
    public static function fromType(string $type): static
    {
        $eType = self::from($type);

        return match ($type) {
            self::Boolean => self::Bool,
            self::Double => self::Float,
            self::Integer => self::Int,

            default => $eType
        };
    }

    /**
     * Wrapper for fromType() but returns null on exception
     *
     * @param string $type
     *
     * @return static|null
     */
    public static function tryFromType(string $type): ?static
    {
        try {
            return self::fromType($type);
        }
        catch (ValueError) {
            return null;
        }
    }

    /**
     * Gets the EType based on a mixed type variable
     *
     * @param mixed $value
     *
     * @return static
     *
     * @throws ValueError if the type is not recognised
     */
    public static function fromMixed(mixed $value): static
    {
        $eType = self::fromType(strtolower(gettype($value)));

        if ($eType === self::Array) {
            if (!Type::isAssoc($value)) {
                $eType = self::ArrayMixed;
                $zerothType = self::tryFromMixed($value[0] ?? null);

                if (Type::of($value)->isArrayOf($zerothType)) {
                    $eType = match ($zerothType) {
                        self::Null => self::ArrayNull,
                        self::Array => self::ArrayArray,
                        self::Bool => self::ArrayBool,
                        self::Float => self::ArrayFloat,
                        self::Int => self::ArrayInt,
                        self::Object => self::ArrayObject,
                        self::Resource => self::ArrayResource,
                        self::ResourceClosed => self::ArrayResourceClosed,
                        self::String => self::ArrayString,
                        self::Dt => self::ArrayDt,
                        self::Json => self::ArrayJson,
                        default => $eType
                    };
                }
            }
        }
        else if ($eType === self::Object) {
            if ($value instanceof DT) {
                $eType = self::Dt;
            }
        }
        else if ($eType === self::String) {
            if (Json::isValid($value)) {
                $eType = self::Json;
            }
        }

        return $eType;
    }

    /**
     * Same as fromMixed() but returns null on Exception
     *
     * @param mixed $value
     *
     * @return static|null
     */
    public static function tryFromMixed(mixed $value): ?static
    {
        try {
            return self::fromMixed($value);
        }
        catch (ValueError) {
            return null;
        }
    }

    /**
     * Checks if a type is available
     *
     * @param string $type The type to check
     *
     * @return bool If the type is available
     */
    public static function exists(string $type): bool
    {
        return !is_null(self::tryFrom($type));
    }

    /**
     * Check if a type is the one we want
     *
     * @param string|self $type
     *
     * @return bool
     */
    public function is(string|self $type): bool
    {
        if (is_string($type)) {
            $type = self::fromType($type);
        }

        return $this === $type;
    }
}
