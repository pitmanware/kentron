<?php
declare(strict_types=1);

namespace Kentron\Support\System;

use Kentron\Support\Type\Type;

/**
 * Wrapper for the _POST constant array
 */
final class Post
{
    /**
     * Get one item from the _POST array
     *
     * @param string      $key  The post index to retrieve
     * @param string|null $type The type to cast to if necessary
     *
     * @return mixed Can return any type
     */
    public static function getOne(string $key, ?string $type = null)
    {
        $value = $_POST[$key] ?? null;

        if (is_null($value)) {
            return null;
        }

        if (is_null($type)) {
            return $value;
        }

        Type::cast($value)->to($type);
    }

    /**
     * Get all items from the _POST array
     *
     * @return array
     */
    public static function getAll(): array
    {
        return $_POST ?? [];
    }
}
