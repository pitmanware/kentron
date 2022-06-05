<?php
declare(strict_types=1);

namespace Kentron\Support;

class File
{
    /**
     * Static helpers
     */

    /**
     * Returns the full path of directory and filename
     *
     * @param string $path
     * @param string ...$addenda
     *
     * @return string
     */
    final public static function path(string $path, string ...$addenda): string
    {
        return $path . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $addenda);
    }

    /**
     * Get the real path
     *
     * @param string $path
     *
     * @return string|null
     */
    final public static function real(string $path): ?string
    {
        return realpath($path) ?: null;
    }

    /**
     * Check if the file exists
     *
     * @param string $path
     *
     * @return bool
     */
    final public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Check if the file is readable
     *
     * @param string $path
     *
     * @return bool
     */
    final public static function isReadable($path): bool
    {
        return is_readable($path);
    }

    /**
     * Check if the file is writeable
     *
     * @param string $path
     *
     * @return bool
     */
    final public static function isWriteable(string $path): bool
    {
        return is_writeable($path);
    }


    /**
     * Check if the file has no content
     *
     * @param string $filePath
     *
     * @return bool
     */
    public static function isEmpty(string $filePath): bool
    {
        return !@filesize($filePath);
    }

    /**
     * Check if the file is a directory
     *
     * @param string $filePath
     *
     * @return bool
     */
    public static function isDir(string $filePath): bool
    {
        return is_dir($filePath);
    }

    /**
     * Checks if the file is a real file and can be read
     *
     * @param string $filePath
     *
     * @return bool
     */
    public static function isValidFile(string $filePath): bool
    {
        return
            self::exists($filePath) &&
            !self::isDir($filePath) &&
            self::isReadable($filePath);
    }

    /**
     * Checks if the directory is a real directory and can be read
     *
     * @param string $dirPath
     *
     * @return bool
     */
    public static function isValidDir(string $dirPath): bool
    {
        return
            self::exists($dirPath) &&
            self::isDir($dirPath) &&
            self::isReadable($dirPath);
    }

    /**
     * Get the file content
     *
     * @param string $path
     *
     * @return string|null
     */
    final public static function get(string $path): ?string
    {
        return @file_get_contents($path) ?: null;
    }

    /**
     * Put content into the file
     *
     * @param string $path
     * @param string|array|resource $fileContent
     *
     * @return bool
     */
    final public static function put(string $path, $fileContent): bool
    {
        return !!@file_put_contents($path, $fileContent);
    }

    /**
     * Unlink the file
     *
     * @param string $path
     *
     * @return bool
     */
    final public static function delete(string $path): bool
    {
        return @unlink($path);
    }
}
