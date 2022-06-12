<?php
declare(strict_types=1);

namespace Kentron\Facade;

final class Device
{
    private static $device;

    public static function getPlatform(): string
    {
        return self::getDevice()->platform;
    }

    public static function getType(): string
    {
        return self::getDevice()->device_type;
    }

    public static function getDevice(): object
    {
        if (!isset(self::$device)) {
            $device = get_browser($_SERVER["HTTP_USER_AGENT"], false);

            if ($device === false) {
                throw new \UnexpectedValueException("browscap.ini is not installed");
            }

            self::$device = $device;
        }

        return self::$device;
    }
}
