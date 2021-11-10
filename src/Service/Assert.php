<?php

namespace Kentron\Service;

final class Assert
{
    public static function equals($operand, $comparand): bool
    {
        return $operand == $comparand;
    }

    public static function same($operand, $comparand): bool
    {
        return $operand === $comparand;
    }

    public static function is($operand, callable $func): bool
    {
        return $func($operand);
    }

    public static function notEquals($operand, $comparand): bool
    {
        return !self::equals($operand, $comparand);
    }

    public static function notSame($operand, $comparand): bool
    {
        return !self::same($operand, $comparand);
    }

    public static function notIs($operand, callable $func): bool
    {
        return !self::is($operand, $func);
    }
}
