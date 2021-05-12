<?php

namespace Kentron\Facade;

use \DateTime;
use Kentron\Exception\CustomTypeError;
use Kentron\Service\Type;

/**
 * Singleton for the DateTime object
 */
final class DT extends DateTime
{
    /**
     * Gets new self at the current timestamp
     *
     * @return self
     */
    public static function now(): self
    {
        return new self();
    }

    /**
     * Gets new self at a specified timestamp
     *
     * @param string $timeStamp
     *
     * @return self
     */
    public static function then(string $timeStamp): self
    {
        return new self($timeStamp);
    }

    /**
     * Increment self by a given number of seconds, minutes etc
     *
     * @param integer $seconds
     * @param integer $minutes
     * @param integer $hours
     * @param integer $days
     * @param integer $months
     * @param integer $years
     *
     * @return self
     */
    public function increment(int $seconds, int $minutes = 0, int $hours = 0, int $days = 0, int $months = 0, int $years = 0): self
    {
        return $this->add(new \DateInterval("P${years}Y${months}M${days}DT${hours}H${minutes}M{$seconds}S"));
    }

    /**
     * Decrement self by a given number of seconds, minutes etc
     *
     * @param integer $seconds
     * @param integer $minutes
     * @param integer $hours
     * @param integer $days
     * @param integer $months
     * @param integer $years
     *
     * @return self
     */
    public function decrement(int $seconds, int $minutes = 0, int $hours = 0, int $days = 0, int $months = 0, int $years = 0): self
    {
        return $this->sub(new \DateInterval("P${years}Y${months}M${days}DT${hours}H${minutes}M{$seconds}S"));
    }

    /**
     * Format self based on a given string,
     * if none given, use ISO 8601
     *
     * @param string $format
     *
     * @return string
     */
    public function format($format = ""): string
    {
        if (!is_string($format)) {
            throw new CustomTypeError(__METHOD__, Type::TYPE_STRING, gettype($format));
        }

        return parent::format($format ?: "c");
    }

    /**
     * Split self into a string array
     *
     * @return string[]
     */
    public function strSplit(): array
    {
        return [
            $this->format("Y"),
            $this->format("m"),
            $this->format("d"),
            $this->format("H"),
            $this->format("i"),
            $this->format("s"),
        ];
    }

    /**
     * Split self into an integer array
     *
     * @return int[]
     */
    public function intSplit(): array
    {
        return [
            $this->format("Y"),
            $this->format("n"),
            $this->format("j"),
            $this->format("G"),
            (int) $this->format("i"),
            (int) $this->format("s"),
        ];
    }

    /**
     * Compare self with new self using spaceship operator
     *
     * @param self|null $date
     *
     * @return integer
     */
    public function compare(?self $date = null): int
    {
        $date = $date ?? self::now();
        return $this->format() <=> $date->format();
    }

    /**
     * Allows casting
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->format();
    }
}
