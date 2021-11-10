<?php

namespace Kentron\Service\System;

class Session
{
    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function getSession(): array
    {
        return $_SESSION;
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function unset(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
    }
}
