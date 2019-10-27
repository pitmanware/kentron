<?php

    namespace Kentron\Service\System;

    final class Cookie
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
         * @param string $name     The name of the cookie to store
         * @param mixed  $value    The content of the cookie
         * @param int    $lifeTime The valid period of the cookie in UNIX seconds
         *
         * @return void
         */
        public static function create (string $name, $value, int $lifeTime): void
        {
            setcookie($name, $value, $lifeTime);
            $_COOKIE[$name] = $value;
        }
    }
