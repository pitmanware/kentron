<?php

    namespace Kentron\Proxy;

    final class Cast
    {
        /**
         * Casts to an array
         * @param mixed $value Accepts any type
         */
        public static function castToArray ($value): array
        {
            return (array) $value;
        }

        /**
         * Casts to a boolean
         * @param mixed $value Accepts any type
         */
        public static function castToBool ($value): bool
        {
            return (bool) $value;
        }

        /**
         * Casts to a float
         * @param  mixed $value Accepts anything but an object
         * @return float
         * @throws InvalidArgumentException
         */
        public static function castToFloat ($value): float
        {
            if (is_object($value)) {
                throw new \InvalidArgumentException("Non object expected");
            }

            return (float) $value;
        }

        /**
         * Casts to an integer
         * @param  mixed $value Accepts anything but an object
         * @return int
         * @throws InvalidArgumentException
         */
        public static function castToInt ($value): int
        {
            if (is_object($value)) {
                throw new \InvalidArgumentException("Non object expected");
            }

            return (int) $value;
        }

        /**
         * Casts to an object
         * @param mixed $value Accepts any type
         */
        public static function castToObject ($value): object
        {
            return (object) $value;
        }

        /**
         * Casts to a string
         * @param mixed $value Accepts anything except iterable or object
         * @throws InvalidArgumentException
         */
        public static function castToString ($value): string
        {
            if (is_iterable($value) || is_object($value)) {
                throw new \InvalidArgumentException("Non iterable/object expected");
            }

            return (string) $value;
        }
    }
