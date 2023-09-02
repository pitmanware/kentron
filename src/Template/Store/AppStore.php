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
