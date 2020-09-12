<?php

namespace Kentron\Service\System;

use Kentron\Facade\DT;
use Kentron\Store\Variable\AVariable;

class Cookie
{
    /**
     * Gets a stored cookie by name
     *
     * @param string $name The name of the cookie to retrieve
     *
     * @return string|null
     */
    public static function get (string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Add a new cookie
     *
     * @param string $name        The name of the cookie to store
     * @param mixed  $value       The content of the cookie
     * @param DT     $dateExpires The expiry date
     *
     * @return void
     */
    public static function set (string $name, $value, DT $dateExpires): void
    {
        setcookie(
            $name,
            $value,
            [
                "expires" => $dateExpires->format("U"),
                "path" => "/",
                "domain" => Client::getDomain(),
                "secure" => !AVariable::onDev(),
                "httpOnly" => true,
                "samesite" => "Strict"
            ]
        );
        $_COOKIE[$name] = $value;
    }

    public static function unset (string $name): void
    {
        self::set($name, null, DT::now());
    }

    public static function logout (): void
    {
        self::unset(session_name());
    }
}
