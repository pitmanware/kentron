<?php

    namespace Kentron\Service;

    use Kentron\Exception\InvalidRegexException;

    final class Code
    {
        /**
         * The amount of codes to be returned
         * @var int $count
         */
        private $count = 1;

        /**
         * An exclusion list of codes to not produce
         * @var array $exclude
         */
        private $exclude = [];

        /**
         * The length of the codes to be returned
         * @var int $length
         */
        private $length = 4;

        /**
         * Regex to match ignored characters
         * @var string $regex Defaults to special characters produced by base64
         */
        private $regex = "/[\+\/\=]/";

        /**
         * Decides whether to include vowels and vowel-like numbers
         * @var bool $safeMode
         */
        private $safeMode = true;

        /**
         * Getters
         */

        /**
         * Creates an array of unique codes based off the given parameters
         * @return array
         */
        public function get (): array
        {
            $codes = [];

            for ($i = 0; $i < $this->count; $i++) {
                do {
                    $code = $this->generate();
                }
                while (in_array($code, $codes) || in_array($code, $this->exclude));

                $codes[] = $code;
            }

            return $codes;
        }

        /**
         * Get codes containing only alpha characters
         * @return array
         */
        public function getAlpha (): array
        {
            if ($this->safeMode) {
                $this->setNegativeRegex("/[^BCDFGHJKLMNPQRSTVWXYZ]/");
            }
            else {
                $this->setNegativeRegex("/[^ABCDEFGHIJKLMNOPQRSTUVWXYZ]/");
            }

            return $this->get();
        }

        /**
         * Get codes containing only digits
         * @return array
         */
        public function getDigit (): array
        {
            if ($this->safeMode) {
                $this->setNegativeRegex("/[^23456789]/");
            }
            else {
                $this->setNegativeRegex("/[^0123456789]/");
            }

            return $this->get();
        }

        /**
         * Get codes containing only alphanumeric characters
         * @return array
         */
        public function getAlphaNumeric (): array
        {
            if ($this->safeMode) {
                $this->setNegativeRegex("/[^BCDFGHJKLMNPQRSTVWXYZ23456789]/");
            }
            else {
                $this->setNegativeRegex("/[^ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789]/");
            }

            return $this->get();
        }

        /**
         * Setters
         */

        /**
         * Set the length of the expected code to be returned
         * @param int $length
         * @return void
         */
        public function setLength (int $length): void
        {
            if (count($length) > 0) {
                $this->length = $length;
            }
        }

        /**
         * Set the amount of generated codes to be returned
         * @param int $count
         * @return void
         */
        public function setCount (int $count): void
        {
            if (count($count) > 0) {
                $this->count = $count;
            }
        }

        /**
         * Set the regex to be used in generating the codes
         * @param string $regex This is an exclusive regex. Anything matched will be removed
         * @return void
         * @throws InvalidRegexException
         */
        public function setNegativeRegex (string $regex): void
        {
            if (@preg_match($regex, null) === false) {
                throw new \InvalidRegexException("Supplied regex is invalid");
            }

            $this->regex = $regex;
        }

        /**
         * Set an exclusion list
         * @param array $exclude Any code generated in this list will be ignored
         * @return void
         */
        public function setExclude (array $exclude): void
        {
            $this->exclude = $exclude;
        }

        /**
         * Set the safe mode. If active, all vowels will be removed to prevent against expletives
         * @param bool $safeMode
         * @return void
         */
        public function setSafeMode (bool $safeMode): void
        {
            $this->safeMode = $safeMode;
        }

        /**
         * Generates one code based off the regex
         * @return string
         */
        private function generate (): string
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
