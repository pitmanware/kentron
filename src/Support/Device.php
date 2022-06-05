<?php
declare(strict_types=1);

namespace Kentron\Support;

use Kentron\Support\System\Client;
use Kentron\Support\Type\Type;

final class Device
{
    private static array $device;

    public static function getNamePattern(): ?string
    {
        return Type::getProperty(self::getDevice(), "browser_name_pattern") ?: null;
    }

    public static function getPlatform(): ?string
    {
        return Type::getProperty(self::getDevice(), "platform") ?: null;
    }

    public static function getType(): ?string
    {
        return Type::getProperty(self::getDevice(), "device_type") ?: null;
    }

    public static function getBrowser(): ?string
    {
        return Type::getProperty(self::getDevice(), "browser") ?: null;
    }

    public static function getVersion(): ?string
    {
        return Type::getProperty(self::getDevice(), "version") ?: null;
    }

    public static function getDevice(): array
    {
        if (isset(self::$device)) {
            return self::$device;
        }

        /** @var array|false $device */
        $device = get_browser(Client::getUserAgent(), true);

        if ($device === false) {
            throw new \UnexpectedValueException("browscap.ini is not installed");
        }

        self::$device = $device;

        return self::$device;
    }
}
