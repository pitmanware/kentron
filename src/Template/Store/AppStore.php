<?php
declare(strict_types=1);

namespace Kentron\Template\Store;

use Kentron\Entity\TransportEntity;

/**
 * Store for the core app functionality
 */
class AppStore extends AStore
{
    protected static TransportEntity $transportEntity;

    public static function getTransportEntity(): TransportEntity
    {
        return self::$transportEntity;
    }

    public static function setTransportEntity(TransportEntity $transportEntity): void
    {
        self::$transportEntity = $transportEntity;
    }

    /**
     * Helpers
     */

    public static function reset(bool $hard = false): void
    {
        if ($hard) {
            self::$transportEntity = null;
        }
    }
}
