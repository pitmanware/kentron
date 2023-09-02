<?php
declare(strict_types=1);

namespace Kentron\Support;

use Kentron\Support\Http\Http;
use Kentron\Support\System\Client;
use Kentron\Support\Type\Type;

final class Geo
{
    private const URL = "http://www.geoplugin.net/json.gp";

    private static array|object|null $geo = null;

    public static function init(): void
    {
        $http = new Http();

        $http->baseUrl = self::URL;
        $http->setGetData(["ip" => Client::getIP()]);
        $http->decodeAsArray = true;

        if (!$http->run()) {
            throw new \UnexpectedValueException($http->errors[0]);
        }

        $geo = $http->getExtracted() ?? [];

        if (Type::getProperty($geo, "geoplugin_status") !== 200) {
            throw new \UnexpectedValueException("GeoPlugin returned a non-200 status");
        }

        self::$geo = $geo;
    }

    public static function getRegionCode(): string
    {
        return self::get("geoplugin_regionCode");
    }

    public static function getRegionName(): string
    {
        return self::get("geoplugin_regionName");
    }

    public static function getCountryCode(): string
    {
        return self::get("geoplugin_countryCode");
    }

    /**
     * Private methods
     */

    private static function get($geoIndex): string
    {
        if (is_null(self::$geo)) {
            self::init();
        }

        return Type::getProperty(self::$geo, $geoIndex);
    }
}
