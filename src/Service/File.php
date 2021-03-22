<?php

namespace Kentron\Service;

final class File
{
    private static $cacheDir;
    private static $tempDir;

    public static function setCacheDir(string $dirPath): void
    {
        if (!self::isValidDir($dirPath) || !self::isWriteable($dirPath)) {
            throw new \UnexpectedValueException("'{$dirPath}' is not a valid directory");
        }
        self::$cacheDir = self::getRealPath($dirPath);
    }

    public static function setTempDir(string $dirPath): void
    {
        if (!self::isValidDir($dirPath) || !self::isWriteable($dirPath)) {
            throw new \UnexpectedValueException("'{$dirPath}' is not a valid directory");
        }
        self::$tempDir = self::getRealPath($dirPath);
    }

    /**
     * Cache
     */

    public static function getCachePath(string $fileName = ""): string
    {
        return self::$cacheDir . $fileName;
    }

    public static function getCacheFile(string $fileName): ?string
    {
        return self::get(self::getCachePath($fileName));
    }

    public static function putCacheFile(string $fileName, $fileContent): bool
    {
        return self::put(self::getCachePath($fileName), $fileContent);
    }

    /**
     * Temp
     */

    public static function getTempPath(string $fileName = ""): string
    {
        return self::$tempDir . $fileName;
    }

    public static function getTempFile(string $fileName): ?string
    {
        return self::get(self::getTempPath($fileName));
    }

    public static function putTempFile(string $fileName, $fileContent): bool
    {
        return self::put(self::getTempPath($fileName), $fileContent);
    }

    /**
     * Helpers
     */

    public static function exists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    public static function isEmpty(string $filePath): bool
    {
        return !@filesize($filePath);
    }

    public static function isDir(string $filePath): bool
    {
        return is_dir($filePath);
    }

    public static function isReadable(string $filePath): bool
    {
        return is_readable($filePath);
    }

    public static function isWriteable(string $filePath): bool
    {
        return is_writeable($filePath);
    }

    public static function isValidFile(string $filePath): bool
    {
        return
            self::exists($filePath) &&
            !self::isDir($filePath) &&
            self::isReadable($filePath);
    }

    public static function isValidDir(string $dirPath): bool
    {
        return
            self::exists($dirPath) &&
            self::isDir($dirPath) &&
            self::isReadable($dirPath);
    }

    public static function getRealPath(string $filePath): string
    {
        $realPath = realpath($filePath);

        if (!$realPath) {
            throw new \UnexpectedValueException("'{$filePath}' is an invalid file");
        }

        return "{$realPath}/";
    }

    public static function get(string $filePath): ?string
    {
        return @file_get_contents($filePath) ?: null;
    }

    public static function put(string $filePath, $fileContent): bool
    {
        return !!@file_put_contents($filePath, $fileContent);
    }
}
