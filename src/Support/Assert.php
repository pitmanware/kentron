<?php
declare(strict_types=1);

namespace Kentron\Support;

use \Closure;

final class Assert
{
    public const OP_SAME = "same";
    public const OP_EQUALS = "eq";
    public const OP_LESS_THAN = "lt";
    public const OP_GREATER_THAN = "gt";
    public const OP_LESS_THAN_EQUALS = "le";
    public const OP_GREATER_THAN_EQUALS = "ge";
    public const OP_SPACESHIP = "sp";

    /**
     * Compares value not type
     *
     * @param mixed $operand
     * @param mixed $comparand
     *
     * @return bool
     */
    public static function equals(mixed $operand, mixed $comparand): bool
    {
        return $operand == $comparand;
    }

    /**
     * Compares value and type
     *
     * @param mixed $operand
     * @param mixed $comparand
     *
     * @return bool
     */
    public static function same(mixed $operand, mixed $comparand): bool
    {
        return $operand === $comparand;
    }

    /**
     * Id $operand is less than $comparand
     *
     * @param mixed $operand
     * @param mixed $comparand
     *
     * @return bool
     */
    public static function lessThan(mixed $operand, mixed $comparand): bool
    {
        return $operand < $comparand;
    }

    /**
     * If $operand is greater than $comparand
     *
     * @param mixed $operand
     * @param mixed $comparand
     *
     * @return bool
     */
    public static function greaterThan(mixed $operand, mixed $comparand): bool
    {
        return $operand > $comparand;
    }

    /**
     * If $operand is less than or equal to $comparand
     *
     * @param mixed $operand
     * @param mixed $comparand
     *
     * @return bool
     */
    public static function lessThanEquals(mixed $operand, mixed $comparand): bool
    {
        return $operand <= $comparand;
    }

    /**
     * If $operand is greater than or equal to $comparand
     *
     * @param mixed $operand
     * @param mixed $comparand
     *
     * @return bool
     */
    public static function greaterThanEquals(mixed $operand, mixed $comparand): bool
    {
        return $operand >= $comparand;
    }

    /**
     * If $operand is less than, greater than or equal to $comparand
     *
     * @param mixed $operand
     * @param mixed $comparand
     *
     * @return int
     */
    public static function spaceship(mixed $operand, mixed $comparand): int
    {
        return $operand <=> $comparand;
    }

    /**
     * If $operand passes the callable
     *
     * @param mixed $operand
     * @param callable $func
     *
     * @return bool
     */
    public static function is(mixed $operand, callable $func): bool
    {
        return $func($operand);
    }

    /**
     * Get a callable based on the operator
     *
     * @param string $operator
     *
     * @return Closure
     */
    public static function parseOperator(string $operator): Closure
    {
        return match ($operator) {
            self::OP_SAME                => Closure::fromCallable(fn() => self::same(...func_get_args())),
            self::OP_EQUALS              => Closure::fromCallable(fn() => self::equals(...func_get_args())),
            self::OP_LESS_THAN           => Closure::fromCallable(fn() => self::lessThan(...func_get_args())),
            self::OP_GREATER_THAN        => Closure::fromCallable(fn() => self::greaterThan(...func_get_args())),
            self::OP_LESS_THAN_EQUALS    => Closure::fromCallable(fn() => self::lessThanEquals(...func_get_args())),
            self::OP_GREATER_THAN_EQUALS => Closure::fromCallable(fn() => self::greaterThanEquals(...func_get_args())),
            self::OP_SPACESHIP           => Closure::fromCallable(fn() => self::spaceship(...func_get_args()))
        };
    }

    /**
     * Get the readable version of the operator
     *
     * @param string $operator
     *
     * @return string
     */
    public static function getReadableOperator(string $operator): string
    {
        return match ($operator) {
            self::OP_SAME,
            self::OP_EQUALS => "=",
            self::OP_GREATER_THAN => ">",
            self::OP_GREATER_THAN_EQUALS => ">=",
            self::OP_LESS_THAN => "<",
            self::OP_LESS_THAN_EQUALS => "<=",
            self::OP_SPACESHIP => "<=>"
        };
    }
}
