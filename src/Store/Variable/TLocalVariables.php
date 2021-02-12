<?php

namespace Kentron\Store\Variable;

trait TLocalVariables
{
    /**
     * The auth ID of the application
     * @var int|null
     */
    private static $authID = null;

    /**
     * The audit ID from the requet
     * @var int
     */
    private static $auditID;

    /**
     * Getters
     */

    public static function getAuthID(): ?int
    {
        return self::$authID;
    }

    public static function getAuditID(): int
    {
        return self::$auditID;
    }

    /**
     * Setters
     */

    public static function setAuthID(int $authID): void
    {
        self::$authID = $authID;
    }

    public static function setAuditID(int $auditID): void
    {
        self::$auditID = $auditID;
    }

    /**
     * Helpers
     */

    public static function resetLocal(): void
    {
        self::$authID = null;
        self::$auditID = null;
    }
}
