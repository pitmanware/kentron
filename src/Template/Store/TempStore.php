<?php
declare(strict_types=1);

namespace Kentron\Template\Store;

use \Error;

/**
 * Store for temp variables
 */
class TempStore extends AStore
{
    private static array $store = [];

    /**
     * Clear any saved variables
     *
     * @param bool $hard
     *
     * @return void
     */
    public static function reset(bool $hard = false): void
    {
        self::$store = [];
    }

    /**
     * Return a saved variable by key
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws Error If the store item has not been set
     */
    public static function get(string $key): mixed
    {
        if (!isset(self::$store[$key])) {
            throw new Error("Store item '{$key}' is not set");
        }

        return self::$store[$key];
    }

    /**
     * Save a variable statically
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        self::$store[$key] = $value;
    }
}
