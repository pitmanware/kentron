<?php

    namespace Kentron\Proxy\System;

    use Kentron\Template\IRequest;
    use Utils\Proxy\Cast;

    /**
     * Wrapper for the _POST constant array
     */
    final class Post implements IRequest
    {
        /**
         * Get one item from the _POST array
         * @param  string       $key    The post index to retrieve
         * @param  string|null  $type   The type to cast to if necessary
         * @return mixed                Can return any type
         */
        public static function getOne (string $key, ?string $type = null)
        {
            $value = $_POST[$key];

            if (empty($value)) {
                return null;
            }

            if (is_null($type)) {
                return $value;
            }

            $typeMethod = Cast::getTypeMethod($type);
            return Cast::$typeMethod($value);
        }

        /**
         * Get all items from the _POST array
         * @return array
         */
        public static function getAll (): array
        {
            return $_POST ?? [];
        }
    }
