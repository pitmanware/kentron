<?php

    namespace Kentron\Proxy;

    class Code
    {
        private $count      = 1,
                $exclude    = [],
                $length     = 4,
                $regex      = "/[\+\/\=]/",
                $safeMode   = true;

        /**
         *
         * Getters
         *
         */
        final public function get (): array
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

        final public function getAlpha (): array
        {
            if ($this->safeMode) {
                $this->setRegex("/[^BCDFGHJKLMNPQRSTVWXYZ]/");
            }
            else {
                $this->setRegex("/[^ABCDEFGHIJKLMNOPQRSTUVWXYZ]/");
            }

            return $this->get();
        }

        final public function getDigit (): array
        {
            if ($this->safeMode) {
                $this->setRegex("/[^23456789]/");
            }
            else {
                $this->setRegex("/[^0123456789]/");
            }

            return $this->get();
        }

        final public function getAlphaNumeric (): array
        {
            if ($this->safeMode) {
                $this->setRegex("/[^BCDFGHJKLMNPQRSTVWXYZ23456789]/");
            }
            else {
                $this->setRegex("/[^ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789]/");
            }

            return $this->get();
        }

        /**
         *
         * Setters
         *
         */
        final public function setLength (int $length): void
        {
            if (count($length) > 0) {
                $this->length = $length;
            }
        }

        final public function setCount (int $count): void
        {
            if (count($count) > 0) {
                $this->count = $count;
            }
        }

        final public function setRegex (string $regex): void
        {
            $this->regex = $regex;
        }

        final public function setExclude (array $exclude): void
        {
            $this->exclude = $exclude;
        }

        final public function setSafeMode (bool $safeMode): void
        {
            // Safe mode removed vowels and vowel-like digits from codes to prevent against expletives
            $this->safeMode = $safeMode;
        }

        /**
         *
         * Private function to generate a code based off the regex
         *
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
