<?php

namespace Kentron\Store;

abstract class AToken implements IStore
{
    /**
     * The bearer token
     *
     * @var string
     */
    private static $token;

    /**
     * Get the token if it is valid, if not, attempt to retrieve it
     *
     * @return boolean
     */
    final public static function get(): string
    {
        if (is_string(self::$token)) {
            return self::$token;
        }

        self::set(self::getToken());

        return self::$token;
    }

    /**
     * Set the token
     *
     * @param string $token
     *
     * @return void
     */
    final public static function set(string $token): void
    {
        self::$token = $token;
    }

    /**
     * Used for getting a token if it is invalid.
     * Should be overridden if set() is unused
     *
     * @return string
     */
    public static function getToken(): string
    {
        throw new \Exception(__METHOD__ . " expects to be overridden");
    }

    /**
     * {@inheritDoc}
     */
    public static function reset(): void
    {
        self::$token = null;
    }
}
