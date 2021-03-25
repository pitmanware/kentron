<?php

namespace Kentron\Service\Provider\Template;

interface IProviderRequestService
{
    public function buildRequest(AProviderEntity $providerEntity): void;
}
