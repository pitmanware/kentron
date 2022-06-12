<?php
declare(strict_types=1);

namespace Kentron\Support\Type;

use Kentron\Facade\DT;
use Kentron\Struct\SType;
use Kentron\Support\Json;

use \InvalidArgumentException;
use \UnexpectedValueException;

trait TCast
{
    private bool $quiet = false;

    public function __construct(
        private mixed $value
    ) {}

    /**
     * Sets value param and allows chaining
     *
     * @param mixed $value
     *
     * @return self
     */
    public static function cast($value): self
    {
        return new self($value);
    }

    /**
     * Reverses quietly()
     *
     * @return self
     */
    public function loudly(): self
    {
        $this->quiet = false;
        return $this;
    }

    /**
     * Doesn't throw exceptions and allows chaining
     *
     * @return self
     */
    public function quietly(): self
    {
        $this->quiet = true;
        return $this;
    }

    /**
     * Gets one of the casting methods
     *
     * @param string $type The type to cast to
     *
     * @return mixed The result of the method call
     *
     * @throws UnexpectedValueException If the given type is unknown
     */
    public function to(string $type): mixed
    {
        return match (strtolower($type)) {
            SType::TYPE_ARRAY   => $this->castToArray($this->value),
            SType::TYPE_BOOL,
            SType::TYPE_BOOLEAN => $this->castToBool($this->value),
            SType::TYPE_FLOAT,
            SType::TYPE_DOUBLE  => $this->castToFloat($this->value),
            SType::TYPE_INT,
            SType::TYPE_INTEGER => $this->castToInt($this->value),
            SType::TYPE_OBJECT  => $this->castToObject($this->value),
            SType::TYPE_STRING  => $this->castToString($this->value),
            SType::TYPE_DT      => $this->castToDT($this->value),
            SType::TYPE_JSON    => $this->castToJson($this->value),

            default => $this->quiet ? $this->value : throw new UnexpectedValueException("$type is not a valid type")
        };
    }

    /**
     * Chainable cast to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->castToArray($this->value);
    }

    /**
     * Casts to an array
     *
     * @param mixed $value Accepts any type
     *
     * @return array
     */
    public static function castToArray($value): array
    {
        if (is_string($value)) {
            $json = Json::toArray($value);

            if (is_array($json)) {
                return $json;
            }
        }
        else if (is_array($value)) {
            $json = Json::toArray(Json::toString($value));

            if (is_array($json)) {
                return $json;
            }
        }

        return (array) $value;
    }

    /**
     * Chainable cast to a boolean
     *
     * @return bool
     */
    public function toBool(): bool
    {
        return self::castToBool($this->value);
    }

    /**
     * Casts to a boolean
     *
     * @param mixed $value Accepts any type
     *
     * @return bool
     */
    public static function castToBool($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    /**
     * Chainable cast to a float
     *
     * @return float|object Float or original object on quiet exception
     *
     * @throws InvalidArgumentException If the type to be coerced is an object
     */
    public function toFloat(): float|object
    {
        return self::castToFloat($this->value, $this->quiet);
    }

    /**
     * Casts to a float
     *
     * @param mixed $value Accepts anything but an object
     * @param bool $quiet If true, does not throw on error
     *
     * @return float|object Float or original object on quiet exception
     *
     * @throws InvalidArgumentException If the type to be coerced is an object
     */
    public static function castToFloat(mixed $value, bool $quiet = false): float|object
    {
        if (is_object($value)) {
            if ($quiet) {
                return $value;
            }

            throw new InvalidArgumentException("Non object expected");
        }

        return (float) $value;
    }

    /**
     * Chainable cast to an integer
     *
     * @return int|object Int or original object on quiet exception
     *
     * @throws InvalidArgumentException If the type to be coerced is an object
     */
    public function toInt(): int|object
    {
        return self::castToInt($this->value, $this->quiet);
    }

    /**
     * Casts to an integer
     *
     * @param mixed $value Accepts anything but an object
     * @param bool $quiet If true, does not throw on error
     *
     * @return int|object Int or original object on quiet exception
     *
     * @throws InvalidArgumentException If the type to be coerced is an object
     */
    public static function castToInt(mixed $value, bool $quiet = false): int|object
    {
        if (is_object($value)) {
            if ($quiet) {
                return $value;
            }

            throw new InvalidArgumentException("Non object expected");
        }

        return (int) $value;
    }

    /**
     * Casts to an object
     *
     * @return object
     */
    public function toObject(): object
    {
        return self::castToObject($this->value);
    }

    /**
     * Casts to an object
     *
     * @param mixed $value Accepts any type
     *
     * @return object
     */
    public static function castToObject(mixed $value): object
    {
        if (is_string($value)) {
            $json = Json::toObject($value);

            if (is_object($json)) {
                return $json;
            }
        }
        else if (is_array($value)) {
            $json = Json::toObject(Json::toString($value));

            if (is_object($json)) {
                return $json;
            }
        }

        return (object) $value;
    }

    /**
     * Chainable cast to a string
     *
     * @return string|array|object String or original array|object on quiet exception
     *
     * @throws InvalidArgumentException If the type to be coerced is an object or array
     */
    public function toString(): string|array|object
    {
        return self::castToString($this->value, $this->quiet);
    }

    /**
     * Casts to a string
     *
     * @param mixed $value Accepts anything except array or object
     * @param bool $quiet If true, does not throw on error
     *
     * @return string|array|object String or original array|object on quiet exception
     *
     * @throws InvalidArgumentException If the type to be coerced is an object or array
     */
    public static function castToString(mixed $value, bool $quiet = false): string|array|object
    {
        if ($value instanceof DT) {
            return $value->format();
        }

        if (is_array($value) || is_object($value)) {
            if ($quiet) {
                return $value;
            }

            throw new InvalidArgumentException("Non array/object expected");
        }

        return (string) $value;
    }

    /**
     * Chainable cast to DT
     *
     * @return DT|mixed DT or original value on quiet exception
     *
     * @throws InvalidArgumentException If the type to be coerced is not an int or string
     */
    public function toDT(): mixed
    {
        return self::castToDT($this->value, $this->quiet);
    }

    /**
     * Casts to DT
     *
     * @param string|int $value Expects string or null
     * @param bool $quiet If true, does not throw on error
     *
     * @return DT|mixed DT or original value on quiet exception
     *
     * @throws InvalidArgumentException If the type to be coerced is not an int or string
     */
    public static function castToDT(mixed $value, bool $quiet = false): mixed
    {
        if (is_string($value) || is_int($value)) {
            return new DT($value);
        }

        if ($quiet) {
            return $value;
        }

        throw new \InvalidArgumentException("Could not create DT with type " . gettype($value));
    }

    /**
     * Chainable cast to JSON string
     *
     * @return string|mixed string or original value on quiet exception
     *
     * @throws InvalidArgumentException If the type to be coerced is not a string, array or object
     */
    public function toJson(): mixed
    {
        return self::castToJson($this->value, $this->quiet);
    }

    /**
     * Casts to JSON string
     *
     * @param array|object $value
     * @param bool $quiet If true, does not throw on error
     *
     * @return string|mixed string or original value on quiet exception
     *
     * @throws InvalidArgumentException If the type to be coerced is not a string, array or object
     */
    public static function castToJson(mixed $value, bool $quiet = false): mixed
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        if ($quiet) {
            return $value;
        }

        throw new InvalidArgumentException("Could not call json_encode() with type " . gettype($value));
    }
}
