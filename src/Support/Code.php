<?php
declare(strict_types=1);

namespace Kentron\Support;

use Kentron\Exception\InvalidRegexException;

final class Code
{
    /** The amount of codes to be returned */
    private int $count = 1;

    /** The length of the codes to be returned */
    private int $length = 4;

    /** Decides whether to include vowels and vowel-like numbers */
    private bool $safeMode = true;

    /**
     * Regex to match ignored characters
     * Defaults to special characters produced by base64
     */
    private string $regex = "/[\+\/\=]/";

    /**
     * An exclusion list of codes to not produce
     * @var string[] $exclude
     */
    private array $exclude = [];

    /**
     * Getters
     */

    /**
     * Creates an array of unique codes based off the given parameters
     *
     * @return array
     */
    public function get(): array
    {
        $codes = [];
        $count = $this->count;

        while ($count--)
        {
            do
            {
                $code = $this->generate();
            }
            while (in_array($code, $codes) || in_array($code, $this->exclude));

            $codes[] = $code;
        }

        return $codes;
    }

    /**
     * Get codes containing only alpha characters
     *
     * @return array
     */
    public function getAlpha(): array
    {
        $this->regex = $this->safeMode ? "/[^BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz]/" : "/[^A-Za-z]/";

        return $this->get();
    }

    /**
     * Get codes containing only digits
     *
     * @return array
     */
    public function getDigit(): array
    {
        $this->regex = $this->safeMode ? "/[^2-9]/" : "/[^0-9]/";

        return $this->get();
    }

    /**
     * Get codes containing only alphanumeric characters
     *
     * @return array
     */
    public function getAlphaNumeric(): array
    {
        $this->regex = $this->safeMode ? "/[^BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz2-9]/" : "/[^A-Za-z0-9]/";

        return $this->get();
    }

    /**
     * Setters
     */

    /**
     * Set the length of the expected code to be returned
     *
     * @param int $length
     *
     * @return void
     */
    public function setLength(int $length): void
    {
        if ($length > 0) {
            $this->length = $length;
        }
    }

    /**
     * Set the amount of generated codes to be returned
     *
     * @param int $count
     *
     * @return void
     */
    public function setCount(int $count): void
    {
        if ($count > 0) {
            $this->count = $count;
        }
    }

    /**
     * Set the regex to be used in generating the codes
     *
     * @param string $regex This is an exclusive regex. Anything matched will be removed
     *
     * @return void
     *
     * @throws InvalidRegexException
     */
    public function setRegex(string $regex): void
    {
        if (@preg_match($regex, "") === false) {
            throw new InvalidRegexException("Supplied regex is invalid");
        }

        $this->regex = $regex;
    }

    /**
     * Set an exclusion list
     *
     * @param array $exclude Any code generated in this list will be ignored
     *
     * @return void
     */
    public function setExclude(array $exclude): void
    {
        $this->exclude = $exclude;
    }

    /**
     * Set the safe mode. If active, all vowels will be removed to prevent against expletives
     *
     * @param bool $safeMode
     *
     * @return void
     */
    public function setSafeMode(bool $safeMode): void
    {
        $this->safeMode = $safeMode;
    }

    /**
     * Generates one code based off the regex
     *
     * @return string
     */
    private function generate(): string
    {
        return substr(
            str_shuffle(
                str_repeat(
                    preg_replace(
                        $this->regex,
                        "",
                        base64_encode(
                            random_bytes(
                                $this->length * 16
                            )
                        )
                    ),
                    $this->length
                )
            ),
            0,
            $this->length
        );
    }
}
