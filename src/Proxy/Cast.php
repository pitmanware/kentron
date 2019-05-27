<?php

    namespace Kentron\Proxy;

    final class Cast
    {
        public const    TYPE_ARRAY  = "array",
                        TYPE_BOOL   = "bool",
                        TYPE_FLOAT  = "float",
                        TYPE_INT    = "int",
                        TYPE_OBJECT = "object";

        public static function getTypeMethod (string $type): string
        {
            switch ($type) {
                case $this::TYPE_ARRAY:
                    return "castToArray";
                    break;
                case $this::TYPE_BOOL:
                    return "castToBool";
                    break;
                case $this::TYPE_FLOAT:
                    return "castToFloat";
                    break;
                case $this::TYPE_INT:
                    return "castToInt";
                    break;
                case $this::TYPE_OBJECT:
                    return "castToObject";
                    break;
                default:
                    return "castToString";
                    break;
            }
        }

        public static function castToArray ($value): array
        {
            return (array) $value;
        }

        public static function castToBool ($value): bool
        {
            return (bool) $value;
        }

        public static function castToFloat ($value): float
        {
            return (float) $value;
        }

        public static function castToInt ($value): int
        {
            return (int) $value;
        }

        public static function castToObject ($value): object
        {
            return (object) $value;
        }

        public static function castToString ($value): string
        {
            return (string) $value;
        }
    }
