<?php
declare(strict_types=1);

namespace Kentron\Support;

use \Closure;
use \Error;
use Kentron\Enum\EOperator;

final class Evaluate
{
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
     * @param EOperator|string $operator
     *
     * @return Closure
     */
    public static function parseOperator(EOperator|string $operator): Closure
    {
        $add      = fn(int|float ...$operands) => self::add(...$operands);
        $subtract = fn(int|float ...$operands) => self::subtract(...$operands);
        $divide   = fn(int|float ...$operands) => self::divide(...$operands);
        $multiply = fn(int|float ...$operands) => self::multiply(...$operands);
        $raise    = fn(int|float ...$operands) => self::raise(...$operands);

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
        $operator = is_string($operator) ? EOperator::from($operator) : $operator;

        return match ($operator) {
            EOperator::Add      => Closure::fromCallable($callIfNumeric($add, ...func_get_args())),
            EOperator::Subtract => Closure::fromCallable($callIfNumeric($subtract, ...func_get_args())),
            EOperator::Divide   => Closure::fromCallable($callIfNumeric($divide, ...func_get_args())),
            EOperator::Multiply => Closure::fromCallable($callIfNumeric($multiply, ...func_get_args())),
            EOperator::Raise    => Closure::fromCallable($callIfNumeric($raise, ...func_get_args())),
            EOperator::And      => Closure::fromCallable(fn(bool ...$operands) => self::and(...$operands)),
            EOperator::Or       => Closure::fromCallable(fn(bool ...$operands) => self::or(...$operands))
        };
    }

    /**
     * Get the readable version of the operator
     *
     * @param EOperator|string $operator
     *
     * @return string
     */
    public static function getReadableOperator(EOperator|string $operator): string
    {
        $operator = is_string($operator) ? EOperator::from($operator) : $operator;
        return match ($operator) {
            EOperator::Add => "+",
            EOperator::And => "and",
            EOperator::Divide => "÷",
            EOperator::Multiply => "x",
            EOperator::Or => "or",
            EOperator::Raise => "^",
            EOperator::Subtract => "-"
        };
    }
}
