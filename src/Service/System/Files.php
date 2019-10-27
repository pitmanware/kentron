<?php

    namespace Kentron\Service\System;

    use Kentron\Template\IRequest;

    class Files implements IRequest
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
