<?php

    namespace Kentron\Proxy;

    final class Config
    {
        public static function get (): string
        {
            return file_get_contents(CONFIG_DIR . "Config/Config.json");
        }
    }
