<?php
declare(strict_types=1);

namespace Kentron\Support\System;

final class Client
{
    /**
     * Gets the user agent
     *
     * @return string|null
     */
    public static function getUserAgent(): ?string
    {
        return ($_SERVER["HTTP_USER_AGENT"] ?? "") ?: null;
    }

    /**
     * Gets the IP address of the client
     * @return string|null
     */
    public static function getIP(): ?string
    {
        return (((((($_SERVER["HTTP_CLIENT_IP"]       ?? "")  ?:
                    ($_SERVER["HTTP_X_FORWARDED_FOR"] ?? "")) ?:
                    ($_SERVER["HTTP_X_FORWARDED"]     ?? "")) ?:
                    ($_SERVER["HTTP_FORWARDED_FOR"]   ?? "")) ?:
                    ($_SERVER["HTTP_FORWARDED"]       ?? "")) ?:
                    ($_SERVER["REMOTE_ADDR"]          ?? "")) ?:
                    null;
    }

    public static function getUrl(): string
    {
        return "//" . self::getDomain() . $_SERVER['REQUEST_URI'];
    }

    public static function getQueryString(): array
    {
        return parse_url(self::getUrl(), PHP_URL_QUERY);
    }

    /**
     * Gets the domain name of the server
     * @return string|null
     */
    public static function getDomain(): ?string
    {
        return (($_SERVER["HTTP_HOST"] ?? "") ?: $_SERVER["SERVER_NAME"] ?? "") ?: null;
    }

    public static function isPrivate(): bool
    {
        return !filter_var(self::getIP(), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    public static function getProtocol (): string
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? null) == 443) ? "https://" : "http://";
    }
}
