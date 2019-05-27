<?php

    namespace Kentron\Template;

    /**
     * For classes that get information from the request constants
     */
    interface IRequest
    {
        public static function getOne (string $key);

        public static function getAll (): array;
    }
