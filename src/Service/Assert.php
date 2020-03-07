<?php

namespace Kentron\Service;

final class Assert
{
    public static function equals ($operand, $comparand): bool
    {
        return $operand == $comparand;
    }

    public static function same ($operand, $comparand): bool
    {
        return $operand === $comparand;
    }
}
