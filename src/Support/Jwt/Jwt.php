<?php
declare(strict_types=1);

namespace Kentron\Support\Jwt;

use \Exception;
use \UnexpectedValueException;
use \DateTime;

use Kentron\Support\Jwt\Entity\Algorithm;
use Kentron\Support\Jwt\Entity\Header;
use Kentron\Support\Jwt\Entity\Payload;

/**
 * JSON Web Token implementation, based on this spec:
 * https://tools.ietf.org/html/rfc7519
 */
final class Jwt
{
    /**
     * Decodes a JWT string into a PHP object.
     *
     * @param string $jwt The JWT
     * @param string $key The Key
     * @param string $alg The algorithm.
     *
     * @return object The JWT's payload
     *
     * @throws UnexpectedValueException Provided JWT was invalid
     * @throws Exception                Provided JWT is trying to be used before it's eligible as defined by 'nbf'
     * @throws Exception                Provided JWT is trying to be used before it's been created as defined by 'iat'
     * @throws Exception                Provided JWT has since expired, as defined by the 'exp' claim
     */
    public static function decode(string $jwt, string $key, string $alg): Payload
    {
        Algorithm::exists($alg);

        [$headb64, $bodyb64, $cryptob64] = array_pad(explode('.', $jwt), 3, null);

        if (is_null($bodyb64) || is_null($cryptob64)) {
            throw new UnexpectedValueException('Wrong number of segments');
        }

        $header = (new Header())->fromString(self::urlsafeB64Decode($headb64));
        $header->verify();

        $payload = (new Payload())->fromString(self::urlsafeB64Decode($bodyb64));
        $signature = self::urlsafeB64Decode($cryptob64);

        if (is_null($signature)) {
            throw new UnexpectedValueException('Invalid signature encoding');
        }

        // Check the algorithm
        if (!Algorithm::hashEquals($alg, $header->alg)) {
            throw new UnexpectedValueException('Incorrect key for this algorithm');
        }
        if (!Algorithm::verify($header->alg, "{$headb64}.{$bodyb64}", $key, $signature)) {
            throw new Exception('Signature verification failed');
        }

        $timestamp = time();

        // Check the nbf if it is defined. This is the time that the
        // token can actually be used. If it's not yet that time, abort.
        if (isset($payload->nbf) && ($payload->nbf > $timestamp)) {
            throw new Exception(
                'Cannot handle token prior to ' . date(DateTime::ISO8601, $payload->nbf)
            );
        }

        // Check that this token has been created before 'now'. This prevents
        // using tokens that have been created for later use (and haven't
        // correctly used the nbf claim).
        if (isset($payload->iat) && ($payload->iat > $timestamp)) {
            throw new Exception(
                'Cannot handle token prior to ' . date(DateTime::ISO8601, $payload->iat)
            );
        }

        // Check if this token has expired.
        if (isset($payload->exp) && ($timestamp >= $payload->exp)) {
            throw new Exception('Expired token');
        }

        return $payload;
    }

    /**
     * Converts and signs data into a JWT string.
     *
     * @param Payload $payload The payload object
     * @param Header $header The header elements to attach
     * @param string $key The secret key.
     *
     * @return string A signed JWT
     */
    public static function encode(Payload $payload, Header $header, string $key): string
    {
        $segments = [
            self::urlsafeB64Encode($header->toString()),
            self::urlsafeB64Encode($payload->toString())
        ];

        $signature = Algorithm::sign($header->alg, implode('.', $segments), $key);
        $segments[] = self::urlsafeB64Encode($signature);

        return implode('.', $segments);
    }

    /**
     * Decode a string with URL-safe Base64.
     *
     * @param string $input A Base64 encoded string
     *
     * @return string|null A decoded string
     */
    public static function urlsafeB64Decode($input): ?string
    {
        str_pad(
            $input,
            (int) ceil(strlen($input) / 4) * 4,
            '='
        );

        return base64_decode(strtr($input, '-_', '+/')) ?: null;
    }

    /**
     * Encode a string with URL-safe Base64.
     *
     * @param string $input The string you want encoded
     *
     * @return string The base64 encode of what you passed in
     */
    public static function urlsafeB64Encode($input): string
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}
