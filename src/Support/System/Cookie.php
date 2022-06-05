<?php
declare(strict_types=1);

namespace Kentron\Support\System;

use Kentron\Facade\DT;

class Cookie
{
    public static $headers = [];

    /**
     * Gets a stored cookie by name
     *
     * @param string $name The name of the cookie to retrieve
     *
     * @return string|null
     */
    public static function get(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Add a new cookie
     *
     * @param string $name        The name of the cookie to store
     * @param mixed  $value       The content of the cookie
     * @param DT     $dateExpires The expiry date
     * @param bool   $httpOnly
     *
     * @return void
     */
    public static function set(string $name, $value, DT $dateExpires, bool $httpOnly = true): void
    {
        $domain = Client::getDomain();
        $secure = !Crypt::onDev();
        $expires = $dateExpires->format("U");

        setcookie(
            $name,
            $value,
            [
                "expires" => $expires,
                "path" => "/",
                "domain" => $domain,
                "secure" => $secure,
                "httpOnly" => $httpOnly,
                "samesite" => "Strict"
            ]
        );
        $_COOKIE[$name] = $value;

        $header = sprintf(
            '%s=%s; expires=%s; path=/; domain=%s; samesite=Strict',
            $name,
            urlencode($value),
            $expires,
            $domain
        );
        $header .= $httpOnly ? '; httponly' : '';
        $header .= $secure ? '; secure' : '';

        self::$headers[] = $header;
    }

    public static function unset(string $name): void
    {
        self::set($name, null, DT::now());
    }

    public static function logout(): void
    {
        self::unset(session_name());
    }

    public static function getHeaders(): array
    {
        return self::$headers;
    }
}
