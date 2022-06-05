<?php
declare(strict_types=1);

namespace Kentron\Support\System;

use Kentron\Support\Assert;

final class Crypt
{
    public const ENV_DEV = 1;
    public const ENV_UAT = 2;
    public const ENV_LIVE = 3;

    protected const DEFAULT_CIPHER = "AES-256-OFB";

    /** The environment */
    public static int|null $environment = null;

    /** The encryption cipher */
    public static string $cipher = self::DEFAULT_CIPHER;

    /** The base64 decoded random byte string initialisation vector to be used on encryption/decryption */
    public static string $initialisationVector = "";

    /** The key to be used on encryption/decryption */
    public static string $key = "";

    /**
     * Helpers
     */

    /**
     * Encrypts a value for the database
     *
     * @param string $toDecrypt The encrypted variable
     *
     * @return string
     */
    public static function encrypt(string $toDecrypt): string
    {
        return openssl_encrypt($toDecrypt, self::$cipher, self::$key, 0, self::$initialisationVector);
    }

    /**
     * Decrypts a value from the database
     *
     * @param string $toDecrypt The encrypted variable
     *
     * @return string
     */
    public static function decrypt(string $toDecrypt): string
    {
        return openssl_decrypt($toDecrypt, self::$cipher, self::$key, 0, self::$initialisationVector);
    }

    /**
     * Returns true if the environment is set to development
     *
     * @return bool
     */
    public static function onDev(): bool
    {
        return Assert::same(self::$environment, self::ENV_DEV);
    }

    /**
     * Returns true if the environment is set to UAT
     *
     * @return bool
     */
    public static function onUAT(): bool
    {
        return Assert::same(self::$environment, self::ENV_UAT);
    }

    /**
     * Returns true if the environment is set to live
     *
     * @return bool
     */
    public static function onLive(): bool
    {
        return Assert::same(self::$environment, self::ENV_LIVE);
    }
}
