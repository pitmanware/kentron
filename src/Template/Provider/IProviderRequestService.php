<?php

namespace Kentron\Template\Provider;

use Kentron\Template\Provider\Entity\AProviderEntity;

interface IProviderRequestService
{
    public function buildRequest(AProviderEntity $providerEntity): void;
}
