<?php

namespace Kentron\Facade;

use Kentron\Service\Http\Entity\HttpEntity;
use Kentron\Service\Http\HttpService;
use Kentron\Service\System\Client;

final class Geo
{
    private const URL = "http://www.geoplugin.net/json.gp";

    private static $geo;

    public static function init (): void
    {
        $httpEntity = new HttpEntity();

        $httpEntity->setBaseUrl(self::URL);
        $httpEntity->setGetData(["ip" => Client::getIP()]);
        $httpEntity->setDecoding($httpEntity::DECODE_JSON);
        $httpEntity->setDecodeToArray();

        if (!HttpService::run($httpEntity)) {
            throw new \UnexpectedValueException($httpEntity->getErrors());
        }

        $geo = $httpEntity->getExtracted();

        if ($geo["geoplugin_status"] !== 200) {
            throw new \UnexpectedValueException("GeoPlugin returned a non-200 status");
        }

        self::$geo = $geo;
    }

    public static function getRegionCode (): string
    {
        return self::get("geoplugin_regionCode");
    }

    public static function getRegionName (): string
    {
        return self::get("geoplugin_regionName");
    }

    public static function getCountryCode (): string
    {
        return self::get("geoplugin_countryCode");
    }

    /**
     * Private methods
     */

    private static function get ($geoIndex): string
    {
        if (is_null(self::$geo)) {
            self::init();
        }

        return self::$geo[$geoIndex];
    }
}
