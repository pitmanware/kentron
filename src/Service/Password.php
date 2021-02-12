<?php

namespace Kentron\Service;

final class Password
{
    public static function hash(string $password): ?string
    {
        return password_hash($password, PASSWORD_BCRYPT) ?: null;
    }

    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
