<?php

namespace Kentron\Service\System;

use Kentron\Service\Type;

/**
 * Wrapper for the _Get constant array
 */
final class Get
{
    /**
     * Get one item from the _GET array
     *
     * @param string      $key  The post index to retrieve
     * @param string|null $type The type to cast to if necessary
     *
     * @return mixed Can return any type
     */
    public static function getOne(string $key, ?string $type = null)
    {
        $value = $_GET[$key] ?? null;

        if (is_null($value)) {
            return null;
        }

        if (is_null($type)) {
            return $value;
        }

        return Type::cast($value)::to($type);
    }

    /**
     * Get all items from the _GET array
     *
     * @return array
     */
    public static function getAll(): array
    {
        return $_GET ?? [];
    }
}
