<?php

namespace Kentron\Service\System;

final class Client
{
    /**
     * Gets the user agent
     * 
     * @return string
     */
    public static function getUserAgent (): string
    {
        return $_SERVER["HTTP_USER_AGENT"] ?? "";
    }

    /**
     * Gets the IP address of the client
     * @return string
     */
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

    /**
     * Gets the domain name of the server
     * @return string
     */
    public static function getDomain (): string
    {
        return $_SERVER["HTTP_HOST"] ?? $_SERVER["SERVER_NAME"] ?? "";
    }
}
