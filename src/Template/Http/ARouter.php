<?php
declare(strict_types=1);

namespace Kentron\Template\Http;

use Kentron\Template\IApp;

abstract class ARouter
{
    protected static string $apiRoutePath;
    protected static string $ajaxRoutePath;
    protected static string $systemRoutePath;

    /**
     * Loads all routes
     *
     * @param IApp $app This application
     */
    public static function loadAllRoutes(IApp $app): void
    {
        self::loadApiRoutes($app);
        self::loadAjaxRoutes($app);
        self::loadSystemRoutes($app);
    }

    /**
     * Load all the API specific routes only
     *
     * @param IApp $app
     *
     * @return void
     */
    protected static function loadApiRoutes(IApp $app): void
    {
        if (is_string(static::$apiRoutePath)) {
            require_once static::$apiRoutePath;
        }
    }

    /**
     * Load all the AJAX specific routes only
     *
     * @param IApp $app
     *
     * @return void
     */
    protected static function loadAjaxRoutes(IApp $app): void
    {
        if (is_string(static::$ajaxRoutePath)) {
            require_once static::$ajaxRoutePath;
        }
    }

    /**
     * Load all the System specific routes only
     *
     * @param IApp $app
     *
     * @return void
     */
    protected static function loadSystemRoutes(IApp $app): void
    {
        if (is_string(static::$systemRoutePath)) {
            require_once static::$systemRoutePath;
        }
    }
}
