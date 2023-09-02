<?php
declare(strict_types=1);

namespace Kentron\Support;

use Kentron\Enum\EAssertion;
use Kentron\Enum\EType;
use Kentron\Template\Entity\ACollectionEntity;
use Kentron\Template\Entity\ACoreCollectionEntity;
use Kentron\Template\Entity\TCollection;

use \BackedEnum;
use \Closure;
use \ReflectionClass;
use \Throwable;

final class Assert
{
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
     * If $operand is less than $comparand
     *
     * `strlen()` for string and `count()` for array
     *
     * @param string|int|float|array $operand
     * @param string|int|float|array $comparand
     *
     * @return bool
     */
    public static function lessThan(string|int|float|array $operand, string|int|float|array $comparand): bool
    {
        return self::sanitiseNumeric($operand) < self::sanitiseNumeric($comparand);
    }

    /**
     * If $operand is greater than $comparand
     *
     * `strlen()` for string and `count()` for array
     *
     * @param string|int|float|array $operand
     * @param string|int|float|array $comparand
     *
     * @return bool
     */
    public static function greaterThan(string|int|float|array $operand, string|int|float|array $comparand): bool
    {
        return self::sanitiseNumeric($operand) > self::sanitiseNumeric($comparand);
    }

    /**
     * If $operand is less than or equal to $comparand
     *
     * `strlen()` for string and `count()` for array
     *
     * @param string|int|float|array $operand
     * @param string|int|float|array $comparand
     *
     * @return bool
     */
    public static function lessThanEquals(string|int|float|array $operand, string|int|float|array $comparand): bool
    {
        return self::sanitiseNumeric($operand) <= self::sanitiseNumeric($comparand);
    }

    /**
     * If $operand is greater than or equal to $comparand
     *
     * `strlen()` for string and `count()` for array
     *
     * @param string|int|float|array $operand
     * @param string|int|float|array $comparand
     *
     * @return bool
     */
    public static function greaterThanEquals(string|int|float|array $operand, string|int|float|array $comparand): bool
    {
        return self::sanitiseNumeric($operand) >= self::sanitiseNumeric($comparand);
    }

    /**
     * If $operand is less than, greater than or equal to $comparand
     *
     * `strlen()` for string and `count()` for array
     *
     * @param string|int|float|array $operand
     * @param string|int|float|array $comparand
     *
     * @return int
     */
    public static function spaceship(string|int|float|array $operand, string|int|float|array $comparand): int
    {
        return self::sanitiseNumeric($operand) <=> self::sanitiseNumeric($comparand);
    }

    /**
     * If $object is an instance of $class
     *
     * @param object $object
     * @param string $class
     *
     * @return bool
     */
    public static function is(object $object, string $class): bool
    {
        return $object instanceof $class;
    }

    /**
     * Returns whether a property exists on an iterable scalar, class object or Collection
     *
     * @param array|object $data         The data to extract a value from
     * @param string|int   $property     The key
     * @param bool         $isInitalised Check if an object property also has a value
     *
     * @return bool
     */
    public static function has(object|array $data, string|int $property, bool $isInitialised = false): bool
    {
        if (is_object($data)) {
            if (is_int($property)) {
                if (
                    ($data instanceof ACollectionEntity) ||
                    ($data instanceof ACoreCollectionEntity) ||
                    (in_array(TCollection::class, (new ReflectionClass($data))->getTraitNames()))
                ) {
                    /** @var TCollection $data */
                    return $data->hasEntity($property);
                }
                $property = (string) $property;
            }

            if (property_exists($data, $property)) {
                if ($isInitialised) {
                    return isset($data->{$property});
                }
                return true;
            }

            if ($data instanceof BackedEnum) {
                return !is_null($data::tryFrom($property));
            }

            try {
                return !is_null(constant("{$data}::{$property}"));
            }
            catch (Throwable) {}
        }
        else if (is_array($data)) {
            return isset($data[$property]);
        }

        return false;
    }

    /**
     * If $data contains $match
     *
     * @param array|string $data `in_array()` for array and `str_contains()` for string
     * @param int|float|string $match
     *
     * @return bool
     */
    public static function contains(array|string $data, int|float|string $match): bool
    {
        return match(EType::fromMixed($data)) {
            EType::Array => in_array($match, $data, true),
            EType::String => str_contains($data, (string)$match)
        };
    }

    /**
     * If $data matches a given regex
     *
     * @param string $data
     * @param string $regex
     *
     * @return bool
     */
    public static function matches(string $data, string $regex): bool
    {
        return !!@preg_match($regex, $data);
    }

    /**
     * If $operand passes the callable
     *
     * @param mixed $operand
     * @param callable $func
     *
     * @return bool
     */
    public static function call(mixed $operand, callable $func): bool
    {
        return $func($operand);
    }

    /**
     * Get a callable based on the operator
     *
     * @param EAssertion|string $operator
     *
     * @return Closure
     */
    public static function parseOperator(EAssertion|string $operator): Closure
    {
        $operator = is_string($operator) ? EAssertion::from($operator) : $operator;
        return match ($operator) {
            EAssertion::Same              => Closure::fromCallable(fn() => self::same(...func_get_args())),
            EAssertion::Equals            => Closure::fromCallable(fn() => self::equals(...func_get_args())),
            EAssertion::LessThan          => Closure::fromCallable(fn() => self::lessThan(...func_get_args())),
            EAssertion::GreaterThan       => Closure::fromCallable(fn() => self::greaterThan(...func_get_args())),
            EAssertion::LessThanEquals    => Closure::fromCallable(fn() => self::lessThanEquals(...func_get_args())),
            EAssertion::GreaterThanEquals => Closure::fromCallable(fn() => self::greaterThanEquals(...func_get_args())),
            EAssertion::Spaceship         => Closure::fromCallable(fn() => self::spaceship(...func_get_args())),
            EAssertion::Is                => Closure::fromCallable(fn() => self::is(...func_get_args())),
            EAssertion::Has               => Closure::fromCallable(fn() => self::has(...func_get_args())),
            EAssertion::Contains          => Closure::fromCallable(fn() => self::contains(...func_get_args())),
            EAssertion::Matches           => Closure::fromCallable(fn() => self::matches(...func_get_args()))
        };
    }

    /**
     * Get the readable version of the operator
     *
     * @param EAssertion|string $operator
     *
     * @return string
     */
    public static function getReadableOperator(EAssertion|string $operator): string
    {
        $operator = is_string($operator) ? EAssertion::from($operator) : $operator;
        return match ($operator) {
            EAssertion::Same,
            EAssertion::Equals => "=",
            EAssertion::GreaterThan => ">",
            EAssertion::GreaterThanEquals => ">=",
            EAssertion::LessThan => "<",
            EAssertion::LessThanEquals => "<=",
            EAssertion::Spaceship => "<=>",
            EAssertion::Is => "is a",
            EAssertion::Has => "has key",
            EAssertion::Contains => "contains",
            EAssertion::Matches => "matches"
        };
    }

    private static function sanitiseNumeric(string|int|float|array $value): int|float
    {
        return match (EType::fromMixed($value)) {
            EType::String => strlen($value),
            EType::Array => count($value),
            default => $value
        };
    }
}
