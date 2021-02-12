<?php

namespace Kentron\Service;

final class Config
{
    /**
     * Config directory path, has trailing slash
     *
     * @var string
     */
    private static $configDir;

    public static function setConfigDir(string $configDir): void
    {
        if (!File::isValidDir($configDir)) {
            throw new \UnexpectedValueException("'{$configDir}' is not a valid directory");
        }

        self::$configDir = File::getRealPath($configDir);
    }

    public static function get(): string
    {
        return File::get(self::$configDir . "Config.json");
    }
}
