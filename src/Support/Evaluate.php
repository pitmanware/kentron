<?php
declare(strict_types=1);

namespace Kentron\Support;

use \Closure;
use \Error;

final class Evaluate
{
    public const OP_ADD = "add";
    public const OP_SUBTRACT = "sub";
    public const OP_DIVIDE = "div";
    public const OP_MULTIPLY = "mul";
    public const OP_RAISE = "pow";
    public const OP_AND = "and";
    public const OP_OR = "or";

    /**
     * Add together all parameters
     *
     * @param int|float ...$operands
     *
     * @return int|float
     */
    public static function add(int|float ...$operands): int|float
    {
        return array_sum($operands);
    }

    /**
     * Subtract $operandR from $operandL
     *
     * @param int|float $operandL
     * @param int|float $operandR
     *
     * @return int|float
     */
    public static function subtract(int|float $operandL, int|float $operandR): int|float
    {
        return $operandL - $operandR;
    }

    /**
     * Divide $operandL by $operandR
     *
     * @param int|float $operandL
     * @param int|float $operandR
     *
     * @return int|float
     */
    public static function divide(int|float $operandL, int|float $operandR): int|float
    {
        return $operandL / $operandR;
    }

    /**
     * Get the product of all operands
     *
     * @param int|float ...$operands
     *
     * @return int|float
     */
    public static function multiply(int|float ...$operands): int|float
    {
        return array_product($operands);
    }

    /**
     * Raise $operandL to the power of $operandR
     *
     * @param int|float $operandL
     * @param int|float $operandR
     *
     * @return int|float
     */
    public static function raise(int|float $operandL, int|float $operandR): int|float
    {
        return $operandL ** $operandR;
    }

    /**
     * Get the logical AND of all operands, requires at least two
     *
     * @param bool $operand
     * @param bool ...$operands
     *
     * @return bool
     */
    public static function and(bool $operand, bool ...$operands): bool
    {
        return !in_array(false, [$operand, ...$operands]);
    }

    /**
     * Get the logical OR of all operands, requires at least two
     *
     * @param bool $operand
     * @param bool ...$operands
     *
     * @return bool
     */
    public static function or(bool $operand, bool ...$operands): bool
    {
        return in_array(true, [$operand, ...$operands]);
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
        $add      = fn(int|float ...$operands) => Evaluate::add(...$operands);
        $subtract = fn(int|float ...$operands) => Evaluate::subtract(...$operands);
        $divide   = fn(int|float ...$operands) => Evaluate::divide(...$operands);
        $multiply = fn(int|float ...$operands) => Evaluate::multiply(...$operands);
        $raise    = fn(int|float ...$operands) => Evaluate::raise(...$operands);

        $callIfNumeric = function(callable $callback, ...$operands)
        {
            foreach ($operands as &$operand) {
                if (!is_numeric($operand)) {
                    throw new Error("Equations must be performed against numeric values");
                }
                // Convert to int|float
                $operand = +$operand;
            }

            return call_user_func($callback, ...$operands);
        };

        return match ($operator) {
            Evaluate::OP_ADD      => Closure::fromCallable($callIfNumeric($add, ...func_get_args())),
            Evaluate::OP_SUBTRACT => Closure::fromCallable($callIfNumeric($subtract, ...func_get_args())),
            Evaluate::OP_DIVIDE   => Closure::fromCallable($callIfNumeric($divide, ...func_get_args())),
            Evaluate::OP_MULTIPLY => Closure::fromCallable($callIfNumeric($multiply, ...func_get_args())),
            Evaluate::OP_RAISE    => Closure::fromCallable($callIfNumeric($raise, ...func_get_args())),
            Evaluate::OP_AND      => Closure::fromCallable(fn(bool ...$operands) => Evaluate::and(...$operands)),
            Evaluate::OP_OR       => Closure::fromCallable(fn(bool ...$operands) => Evaluate::or(...$operands))
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
            self::OP_ADD => "+",
            self::OP_AND => "and",
            self::OP_DIVIDE => "÷",
            self::OP_MULTIPLY => "x",
            self::OP_OR => "or",
            self::OP_RAISE => "^",
            self::OP_SUBTRACT => "-"
        };
    }
}
