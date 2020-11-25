<?php

namespace Kentron\Service\Provider\Template;

abstract class AProviderFactory
{
    /** @var AProviderService $service */
    protected static $service;

    abstract public static function init (): string;

    final protected static function setProviderRequestService (IProviderRequest $requestClass): void
    {
        self::$service->setProviderRequestService($requestClass);
    }

    final protected static function setProviderResponseService (IProviderResponse $responseClass): void
    {
        self::$service->setProviderResponseService($responseClass);
    }
}
