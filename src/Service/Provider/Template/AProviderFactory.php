<?php

namespace Kentron\Service\Provider\Template;

abstract class AProviderFactory
{
    /**
     * @var AProviderService
     */
    protected static $service;

    abstract public static function init(): void;

    final protected static function setProviderRequestService(IProviderRequestService $requestClass): void
    {
        self::$service->setProviderRequestService($requestClass);
    }

    final protected static function setProviderResponseService(IProviderResponseService $responseClass): void
    {
        self::$service->setProviderResponseService($responseClass);
    }
}
