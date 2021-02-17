<?php

namespace Kentron\Store\Variable;

use Kentron\Entity\TransportEntity;

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
     * Hold an instance of the Transport Entity statically
     * @var TransportEntity
     */
    private static $transportEntity;

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

    public static function getTransportEntity(): TransportEntity
    {
        return self::$transportEntity;
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

    public static function setTransportEntity(TransportEntity $transportEntity): void
    {
        self::$transportEntity = $transportEntity;
    }

    /**
     * Helpers
     */

    public static function resetLocal(bool $hard = false): void
    {
        self::$authID = null;
        self::$auditID = null;

        if ($hard) {
            self::$transportEntity = null;
        }
    }
}
