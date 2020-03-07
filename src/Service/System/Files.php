<?php

namespace Kentron\Service\System;

final class Files
{
    final public static function getOne (string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    final public static function getAll (): array
    {
        return $_FILES ?? [];
    }
}
