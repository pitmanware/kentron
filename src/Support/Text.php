<?php
declare(strict_types=1);

namespace Kentron\Support;

use \Error;

/**
 * Converts text between cases
 *
 * ### Constants:
 * Simple cases
 * - `Text::LOWER = 1`
 * - `Text::UPPER = 2`
 * - `Text::SENTENCE = 4`
 * - `Text::START = 8`
 *
 * Complex cases
 * - `Text::CAMEL = 16`
 * - `Text::PASCAL = 32`
 * - `Text::SNAKE = 64`
 * - `Text::HAZARD = 128`
 * - `Text::KEBAB = 256`
 * - `Text::TRAIN = 512`
 *
 * _Note: SNAKE and KEBAB preserve the case_
 *
 * ### Examples:
 * ```php
 *      Text::toLower("HELLO WORLD"); // "hello world"
 *      Text::toCamel("Hello World"); // "helloWorld"
 *
 *      Text::transform("hello-world")->from(Text::KEBAB)->to(Text::PASCAL); // "HelloWorld"
 *
 *      // There is no need to convert from simple cases when using CAMEL, PASCAL, HAZARD or TRAIN. These will produce the same effect:
 *      Text::toCamel("hello world"); // "helloWorld"
 *      Text::transform("hello world")->from()->to(Text::CAMEL); // "helloWorld"
 *      Text::transform("hello world")->from(Text::LOWER)->to(Text::CAMEL); // "helloWorld"
 *      Text::transform("hello world")->from(Text::START)->to(Text::CAMEL); // "helloWorld"
 *
 *      // SNAKE and KEBAB do not require upper/lower characters, so their case will be preserved:
 *      Text::toSnake("HELLO WORLD"); // "HELLO_WORLD"
 *      Text::toKebab("HELLO WORLD"); // "HELLO-WORLD"
 *
 *      // If you need to induce a simple case for SNAKE or KEBAB, you can mix one simple case with it:
 *      Text::transform("HELLO WORLD")->from()->to(Text::START | Text::SNAKE); // "Hello_World"
 *
 *      // Note that if you mix simple or complex cases, it will use the lowest mask:
 *      // Masks:                                  16                8             2
 *      Text::transform("helloWorld")->from(Text::CAMEL)->to(Text::START | Text::UPPER); // "HELLO WORLD"
 *      // Masks:                                  16               64            512
 *      Text::transform("helloWorld")->from(Text::CAMEL)->to(Text::SNAKE | Text::TRAIN); // "hello_World"
 * ```
 */
final class Text
{
    // Simple cases
    public const LOWER    = 0b0000000001;
    public const UPPER    = 0b0000000010;
    public const SENTENCE = 0b0000000100;
    public const START    = 0b0000001000;
    // Complex cases
    public const CAMEL    = 0b0000010000;
    public const PASCAL   = 0b0000100000;
    public const SNAKE    = 0b0001000000;
    public const HAZARD   = 0b0010000000;
    public const KEBAB    = 0b0100000000;
    public const TRAIN    = 0b1000000000;

    public string $originalText;
    public string $modifiedText;

    private string $normalised;

    public function __construct(string $text)
    {
        $this->originalText = $text;
    }

    /**
     * Static constructor
     *
     * @param string $text
     *
     * @return self
     */
    public static function transform(string $text): self
    {
        return new self($text);
    }

    /**
     * Convert text from given case
     *
     * @param int $case
     *
     * @return self
     */
    public function from(int $case = self::LOWER): self
    {
        $this->normalised = match ($case) {
            self::LOWER,
            self::UPPER,
            self::SENTENCE,
            self::START => $this->originalText,
            self::CAMEL => $this->fromCamel($this->originalText),
            self::PASCAL => $this->fromPascal($this->originalText),
            self::SNAKE => $this->fromSnake($this->originalText),
            self::HAZARD => $this->fromHazard($this->originalText),
            self::KEBAB => $this->fromKebab($this->originalText),
            self::TRAIN => $this->fromTrain($this->originalText),
            default => throw new Error("Unknown case")
        };

        return $this;
    }

    /**
     * Convert text to given cases
     *
     * @param integer $case
     *
     * @return string
     */
    public function to(int $case): string
    {
        if (!isset($this->normalised)) {
            throw new Error("from() must be called first before using to()");
        }

        $text = match (true) {
            !!($case & self::LOWER) => self::toLower($this->normalised),
            !!($case & self::UPPER) => self::toUpper($this->normalised),
            !!($case & self::SENTENCE) => self::toSentence($this->normalised),
            !!($case & self::START) => self::toStart($this->normalised),
            default => $this->normalised
        };

        $this->modifiedText = match (true) {
            !!($case & self::CAMEL) => self::toCamel($text),
            !!($case & self::PASCAL) => self::toPascal($text),
            !!($case & self::SNAKE) => self::toSnake($text),
            !!($case & self::HAZARD) => self::toHazard($text),
            !!($case & self::KEBAB) => self::toKebab($text),
            !!($case & self::TRAIN) => self::toTrain($text),
            default => $text
        };

        return $this->modifiedText;
    }

    /**
     * From
     */

    private function fromCamel(string $from): string
    {
        return preg_replace('/(?<!^)([A-Z]|\d+)/', ' $0', $from);
    }

    private function fromPascal(string $from): string
    {
        return $this->fromCamel(lcfirst($from));
    }

    private function fromSnake(string $from): string
    {
        return str_replace('_', ' ', $from);
    }

    private function fromHazard(string $from): string
    {
        return $this->fromSnake($from);
    }

    private function fromKebab(string $from): string
    {
        return str_replace('-', ' ', $from);
    }

    private function fromTrain(string $from): string
    {
        return $this->fromKebab($from);
    }

    /**
     * To
     */

    /**
     * Converts string to lower case (once upon a time)
     *
     * @param string $to
     *
     * @return string
     */
    public static function toLower(string $to): string
    {
        return strtolower($to);
    }

    /**
     * Converts string to upper case (ONCE UPON A TIME)
     *
     * @param string $to
     *
     * @return string
     */
    public static function toUpper(string $to): string
    {
        return strtoupper($to);
    }

    /**
     * Converts string to sentence case (Once upon a time)
     *
     * @param string $to
     *
     * @return string
     */
    public static function toSentence(string $to): string
    {
        return ucfirst(self::toLower($to));
    }

    /**
     * Converts string to start case (Once Upon A Time)
     *
     * @param string $to
     *
     * @return string
     */
    public static function toStart(string $to): string
    {
        return ucwords(self::toLower($to), " \t\r\n\f\v'");
    }

    /**
     * Converts string to camel case (onceUponATime)
     *
     * @param string $to
     *
     * @return string
     */
    public static function toCamel(string $to): string
    {
        return lcfirst(self::toPascal($to));
    }

    /**
     * Converts string to pascal case (OnceUponATime)
     *
     * @param string $to
     *
     * @return string
     */
    public static function toPascal(string $to): string
    {
        return str_replace(' ', '', self::toStart($to));
    }

    /**
     * Converts string to snake case (once_upon_a_time)
     *
     * @param string $to
     *
     * @return string
     */
    public static function toSnake(string $to): string
    {
        return str_replace(' ', '_', $to);
    }

    /**
     * Converts string to hazard case (ONCE_UPON_A_TIME)
     *
     * @param string $to
     *
     * @return string
     */
    public static function toHazard(string $to): string
    {
        return self::toUpper(self::toSnake($to));
    }

    /**
     * Converts string to kebab case (once-upon-a-time)
     *
     * @param string $to
     *
     * @return string
     */
    public static function toKebab(string $to): string
    {
        return str_replace(' ', '-', $to);
    }

    /**
     * Converts string to train case (ONCE-UPON-A-TIME)
     *
     * @param string $to
     *
     * @return string
     */
    public static function toTrain(string $to): string
    {
        return self::toUpper(self::toKebab($to));
    }
}
