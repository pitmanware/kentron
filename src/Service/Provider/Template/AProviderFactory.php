<?php

namespace Kentron\Service\Provider\Template;

abstract class AProviderFactory
{
    /** @var AProviderService $service */
    protected static $service;

    protected function __construct(AProviderService $providerService)
    {
        self::$service = $providerService;
    }

    final protected function setProviderRequestService(IProviderRequest $requestClass): void
    {
        self::$service->setProviderRequestService($requestClass);
    }

    final protected function setProviderResponseService(IProviderResponse $responseClass): void
    {
        self::$service->setProviderResponseService($responseClass);
    }
}
