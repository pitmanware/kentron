<?php

    namespace Kentron\Proxy;

    final class Settings
    {
        public static function get (): string
        {
            return file_get_contents(APP_SETTING_DIR . "Config/Config.json");
        }
    }
