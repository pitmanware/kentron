<?php
declare(strict_types=1);

namespace Kentron\Support\Type;

use Kentron\Enum\EType;
use Kentron\Facade\DT;
use Kentron\Support\Json;
use Kentron\Template\Entity\ACollectionEntity;
use Kentron\Template\Entity\ACoreCollectionEntity;
use Kentron\Template\Entity\TCollection;

use \ReflectionClass;
use \stdClass;
use \UnexpectedValueException;

final class Type
{
    use TCast;

    private bool $strict = true;

    public function __construct(
        private mixed $value
    ) {}

    /**
     * Static methods
     */

    /**
     * Checks if a type is available to be coerced
     *
     * @param string $type The type to cast to
     *
     * @return bool If the type is available
     *
     * @see EType::exists();
     */
    public static function exists(string $type): bool
    {
        return EType::exists($type);
    }

    /**
     * Get the string of the type
     *
     * @param mixed $value
     *
     * @return string
     */
    public static function get(mixed $value): string
    {
        if (is_object($value) && !($value instanceof stdClass)) {
            return $value::class;
        }
        else if (is_array($value)) {
            if (!self::isAssoc($value)) {

                $zerothType = EType::fromType($value[0]);
                if (self::of($value)->isArrayOf($zerothType)) {
                    return "{$zerothType->value}[]";
                }

                return "mixed[]";
            }
        }

        return EType::tryFromType($value)->value ?? gettype($value);
    }

    /**
     * Checks if an array is associative or numeric indexed
     *
     * @param array $array The array to check
     *
     * @return bool True is the array is associative
     */
    public static function isAssoc(array $array): bool
    {
        return !!array_filter(array_keys($array), 'is_string');
    }

    /**
     * Checks if a variable can be used in a foreach
     * Also works with callables
     *
     * @param mixed $data The variable to check
     *
     * @return bool True if the variable can be traversed
     */
    public static function isIterable($data): bool
    {
        return is_array($data) || is_object($data);
    }

    /**
     * Returns whether a property exists on an iterable scalar
     *
     * @param array|object $data     The data to extract a value from
     * @param string       $property The key
     *
     * @return bool
     */
    public static function hasProperty(array|object $data, string $property): bool
    {
        if (is_object($data) && property_exists($data, $property)) {
            return true;
        }
        else if (is_array($data) && isset($data[$property])) {
            return true;
        }

        return false;
    }

    /**
     * Returns a property from an iterable scalar
     *
     * @param array|object $data     The data to extract a value from
     * @param string|int   $key The key
     *
     * @return mixed Null if no key exists
     */
    public static function getProperty(array|object $data, string|int $key): mixed
    {
        if (is_object($data)) {
            if (is_int($key)) {
                if (
                    ($data instanceof ACollectionEntity) ||
                    ($data instanceof ACoreCollectionEntity) ||
                    (in_array(TCollection::class, (new ReflectionClass($data))->getTraitNames()))
                ) {
                    /** @var TCollection $data */
                    return $data->getEntity($key);
                }
                $key = (string) $key;
            }

            if (property_exists($data, $key)) {
                return $data->{$key};
            }
        }
        else if (is_array($data) && isset($data[$key])) {
            return $data[$key];
        }

        return null;
    }

    /**
     * Sets value param and allows chaining
     *
     * @param mixed $value
     *
     * @return self
     */
    public static function of($value): self
    {
        return new self($value);
    }

    /**
     * Allow type casting before check
     *
     * @return self
     */
    public function loosley(): self
    {
        $this->strict = false;
        return $this;
    }

    /**
     * Don't allow type casting
     *
     * @return self
     */
    public function strictly(): self
    {
        $this->strict = true;
        return $this;
    }

    /**
     * Checks the value is type
     *
     * @param EType $type The type to check
     *
     * @return bool If the type of the data matches
     *
     * @throws UnexpectedValueException If the given type is unknown
     */
    public function is(EType $type): bool
    {
        return match ($type) {
            EType::TYPE_ARRAY   => $this->isArray($this->value),
            EType::TYPE_BOOL,
            EType::TYPE_BOOLEAN => $this->isBool($this->value),
            EType::TYPE_FLOAT,
            EType::TYPE_DOUBLE  => $this->isFloat($this->value),
            EType::TYPE_INT,
            EType::TYPE_INTEGER => $this->isInt($this->value),
            EType::TYPE_OBJECT  => $this->isObject($this->value),
            EType::TYPE_STRING  => $this->isString($this->value),
            EType::TYPE_DT      => $this->isDT($this->value),
            EType::TYPE_JSON    => Json::isValid($this->value),

            EType::TYPE_ARRAY_ARRAY     => $this->isArrayOf(EType::TYPE_ARRAY),
            EType::TYPE_ARRAY_BOOL,
            EType::TYPE_ARRAY_BOOLEAN   => $this->isArrayOf(EType::TYPE_BOOL),
            EType::TYPE_ARRAY_FLOAT,
            EType::TYPE_ARRAY_DOUBLE    => $this->isArrayOf(EType::TYPE_FLOAT),
            EType::TYPE_ARRAY_INT,
            EType::TYPE_ARRAY_INTEGER   => $this->isArrayOf(EType::TYPE_INT),
            EType::TYPE_ARRAY_OBJECT    => $this->isArrayOf(EType::TYPE_OBJECT),
            EType::TYPE_ARRAY_STRING    => $this->isArrayOf(EType::TYPE_STRING),
            EType::TYPE_ARRAY_DT        => $this->isArrayOf(EType::TYPE_DT),

            default => throw new UnexpectedValueException("$type is not a valid type")
        };
    }

    /**
     * Checks the value is an array of type
     *
     * @param EType $type The type to check
     *
     * @return bool If the type of the data matches
     *
     * @throws UnexpectedValueException If the given type is unknown
     */
    public function isArrayOf(EType $type): bool
    {
        $callable = match ($type) {
            EType::TYPE_ARRAY   => fn($element) => $this->isArray($element),
            EType::TYPE_BOOL,
            EType::TYPE_BOOLEAN => fn($element) => $this->isBool($element),
            EType::TYPE_FLOAT,
            EType::TYPE_DOUBLE  => fn($element) => $this->isFloat($element),
            EType::TYPE_INT,
            EType::TYPE_INTEGER => fn($element) => $this->isInt($element),
            EType::TYPE_OBJECT  => fn($element) => $this->isObject($element),
            EType::TYPE_STRING  => fn($element) => $this->isString($element),
            EType::TYPE_DT      => fn($element) => $this->isDT($element),

            default => throw new UnexpectedValueException("$type is not a valid type")
        };

        if (!is_array($this->value)) {
            return false;
        }

        return count($this->value) === count(array_filter($this->value, $callable));
    }

    /**
     * Chainable method for is()
     *
     * @return bool
     */
    public function anArray(): bool
    {
        return self::isArray($this->value);
    }

    /**
     * Is an array
     *
     * @param mixed $value Accepts any type
     *
     * @return bool
     */
    public static function isArray(mixed $value): bool
    {
        return is_array($value);
    }

    /**
     * Chainable method for is()
     *
     * @return bool
     */
    public function aBool(): bool
    {
        return self::isBool($this->value, $this->strict);
    }

    /**
     * Is a boolean
     *
     * @param mixed $value Accepts any type
     * @param bool $strict Accepts non-bool (0/1, yes/no etc.) value if false
     *
     * @return bool
     */
    public function isBool(mixed $value, bool $strict = false): bool
    {
        return is_bool($strict ? $value : filter_var($value, FILTER_VALIDATE_BOOL));
    }

    /**
     * Chainable method for is()
     *
     * @return bool
     */
    public function aFloat(): bool
    {
        return self::isFloat($this->value, $this->strict);
    }

    /**
     * Is a float
     *
     * @param mixed $value
     * @param bool $strict Accepts non-standard float notation ("1,000.00") values if false
     *
     * @return bool
     */
    public static function isFloat(mixed $value, bool $strict = false): bool
    {
        return is_float($strict ? $value : filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND));
    }

    /**
     * Chainable method for is()
     *
     * @return bool
     */
    public function anInt(): bool
    {
        return self::isInt($this->value, $this->strict);
    }

    /**
     * Is an integer
     *
     * @param mixed $value
     * @param bool $strict Allows type coercion if false
     *
     * @return bool
     */
    public static function isInt(mixed $value, bool $strict = false): bool
    {
        return is_int($strict ? $value : filter_var($value, FILTER_VALIDATE_INT));
    }

    /**
     * Chainable method for is()
     *
     * @return bool
     */
    public function anObject(): bool
    {
        return self::isObject($this->value);
    }

    /**
     * Is an object
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isObject(mixed $value): bool
    {
        return is_object($value);
    }

    /**
     * Chainable method for is()
     *
     * @return bool
     */
    public function aString(): bool
    {
        return self::isString($this->value);
    }

    /**
     * Is a string
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isString(mixed $value): bool
    {
        return is_string($value);
    }

    /**
     * Chainable method for is()
     *
     * @return bool
     */
    public function aDT(): bool
    {
        return self::isDT($this->value);
    }

    /**
     * Is a DT object
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isDT(mixed $value): bool
    {
        return $value instanceof DT;
    }
}
