<?php

    namespace Kentron\Proxy;

    final class Type
    {
        /**
         * Checks if an array is associative or numeric indexed
         *
         * @param array $array The array to check
         *
         * @return bool True is the array is associative
         */
        public static function isAssoc (array $array): bool
        {
            return !!array_filter(array_keys($array), "is_string");
        }
    }
