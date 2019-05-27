<?php

    namespace Kentron\Proxy\System;

    final class Cookie
    {
        public static function get (string $name): ?string
        {
            return $_COOKIE[$name] ?? null;
        }

        public static function create (string $name, $value, $lifeTime): void
        {
            setcookie($name, $value, $lifeTime);
            $_COOKIE[$name] = $value;
        }
    }
