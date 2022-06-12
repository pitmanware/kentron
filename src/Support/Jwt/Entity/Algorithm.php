<?php
declare(strict_types=1);

namespace Kentron\Support\Jwt\Entity;

use DomainException;

final class Algorithm
{
    public const ES384 = "ES384";
    public const ES256 = "ES256";
    public const HS256 = "HS256";
    public const HS384 = "HS384";
    public const HS512 = "HS512";
    public const RS256 = "RS256";
    public const RS384 = "RS384";
    public const RS512 = "RS512";

    public static function exists(string $algorithm): bool
    {
        return match ($algorithm) {
            self::ES384,
            self::ES256,
            self::HS256,
            self::HS384,
            self::HS512,
            self::RS256,
            self::RS384,
            self::RS512 => true
        };

        throw new DomainException('Algorithm not supported');
    }

    /**
     * Sign a string with a given key and algorithm.
     *
     * @param string $algorithm The signing algorithm.
     * @param string $data The data to sign
     * @param string $key The secret key
     *
     * @return string An encrypted message
     *
     * @throws DomainException Unsupported algorithm or bad key supplied
     */
    public static function sign(string $algorithm, string $data, string $key): string
    {
        self::exists($algorithm);

        return match ($algorithm) {
            self::ES256,
            self::RS256 => self::openSslSign("SHA256", $data, $key),
            self::ES384,
            self::RS384 => self::openSslSign("SHA384", $data, $key),
            self::RS512 => self::openSslSign("SHA512", $data, $key),
            self::HS256 => self::hashHmacSign("SHA256", $data, $key),
            self::HS384 => self::hashHmacSign("SHA384", $data, $key),
            self::HS512 => self::hashHmacSign("SHA512", $data, $key)
        };
    }

    /**
     * Verify a signature with the message, key and method. Not all methods
     * are symmetric, so we must have a separate verify and sign method.
     *
     * @param string $algorithm The signing algorithm.
     * @param string $data The data to sign
     * @param string $key The secret key
     * @param string $signature The original signature
     *
     * @return string An encrypted message
     *
     * @throws DomainException Invalid Algorithm, bad key, or OpenSSL failure
     */
    public static function verify(string $algorithm, string $data, string $key, string $signature): bool
    {
        self::exists($algorithm);

        return match ($algorithm) {
            self::ES256,
            self::RS256 => self::openSslVerify("SHA256", $data, $key, $signature),
            self::ES384,
            self::RS384 => self::openSslVerify("SHA384", $data, $key, $signature),
            self::RS512 => self::openSslVerify("SHA512", $data, $key, $signature),
            self::HS256 => self::hashHmacVerify("SHA256", $data, $key, $signature),
            self::HS384 => self::hashHmacVerify("SHA384", $data, $key, $signature),
            self::HS512 => self::hashHmacVerify("SHA512", $data, $key, $signature)
        };
    }

    public static function hashEquals(string $left, string $right): bool
    {
        return hash_equals($left, $right);
    }

    protected static function openSslSign(string $cipher, string $data, string $key): string
    {
        $signature = '';

        if (!openssl_sign($data, $signature, $key, $cipher)) {
            throw new DomainException("OpenSSL unable to sign data");
        }

        return $signature;
    }

    protected static function openSslVerify(string $cipher, string $data, string $key, string $signature): bool
    {
        $success = openssl_verify($data, $signature, $key, $cipher);

        if ($success === -1) {
            throw new DomainException(
                'OpenSSL error: ' . openssl_error_string()
            );
        }

        return !!$success;
    }

    protected static function hashHmacSign(string $cipher, string $data, string $key): string
    {
        return hash_hmac($cipher, $data, $key, true);
    }

    protected static function hashHmacVerify(string $cipher, string $data, string $key, string $signature): bool
    {
        $hash = self::hashHmacSign($cipher, $data, $key);
        return self::hashEquals($hash, $signature);
    }
}
