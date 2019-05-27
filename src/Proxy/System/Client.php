<?php

    namespace Kentron\Proxy\System;

    final class Client
    {
        public static function getUserAgent (): string
        {
            return $_SERVER["HTTP_USER_AGENT"] ?? "";
        }

        public static function getIP (): string
        {
            return  $_SERVER["HTTP_CLIENT_IP"] ??
                    $_SERVER["HTTP_X_FORWARDED_FOR"] ??
                    $_SERVER["HTTP_X_FORWARDED"] ??
                    $_SERVER["HTTP_FORWARDED_FOR"] ??
                    $_SERVER["HTTP_FORWARDED"] ??
                    $_SERVER["REMOTE_ADDR"] ??
                    "";
        }

        public static function getDomain (): string
        {
            return $_SERVER["HTTP_HOST"] ?? $_SERVER["SERVER_NAME"] ?? "";
        }

        public static function getProtocol (): string
        {
            if ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") || $_SERVER["SERVER_PORT"] == 443) {
                return "https://";
            }
            else {
                return "http://";
            }
        }
    }
