<?php
declare(strict_types=1);

namespace Kentron\Support;

final class Json
{
    public static function isValid(mixed $json): bool
    {
        return is_string($json) && !is_null(json_decode($json));
    }

    public static function isArray(mixed $json): bool
    {
        return is_string($json) && !self::isObject($json);
    }

    public static function isObject(mixed $json): bool
    {
        return is_string($json) && is_object(json_decode($json, false));
    }

    public static function toArray(mixed $json): ?array
    {
        $jsonArray = is_string($json) ? json_decode($json, true) : null;
        return is_array($jsonArray) ? $jsonArray : null;
    }

    public static function toObject(mixed $json): ?object
    {
        $jsonObject = is_string($json) ? json_decode($json, false) : null;
        return is_object($jsonObject) ? $jsonObject : null;
    }

    public static function toString(array|object $json): ?string
    {
        return json_encode($json) ?: null;
    }

    public static function extract(mixed $json): array|object|null
    {
        return self::isObject($json) ? self::toObject($json) : self::toArray($json);
    }

    /**
     * Helper method to get a normalised json error
     *
     * @param int|null $errno An error number or json_last_error() is null
     *
     * @return string|null
     */
    public static function handleError(?int $errno = null): ?string
    {
        return match ($errno ?? json_last_error()) {
            JSON_ERROR_NONE => null,
            JSON_ERROR_DEPTH => "The maximum stack depth has been exceeded",
            JSON_ERROR_STATE_MISMATCH => "Invalid or malformed JSON",
            JSON_ERROR_CTRL_CHAR => "Control character error, possibly incorrectly encoded",
            JSON_ERROR_SYNTAX => "Syntax error",
            JSON_ERROR_UTF8 => "Malformed UTF-8 characters, possibly incorrectly encoded",
            JSON_ERROR_RECURSION => "One or more recursive references in the value to be encoded",
            JSON_ERROR_INF_OR_NAN => "One or more NAN or INF values in the value to be encoded",
            JSON_ERROR_UNSUPPORTED_TYPE => "A value of a type that cannot be encoded was given",
            JSON_ERROR_INVALID_PROPERTY_NAME => "A property name that cannot be encoded was given",
            JSON_ERROR_UTF16 => "Malformed UTF-16 characters, possibly incorrectly encode"
        };
    }
}
