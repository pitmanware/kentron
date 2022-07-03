<?php

namespace Kentron\Template\Provider\Service;

use Kentron\Template\Provider\Entity\AProviderRequestEntity;

interface IProviderRequestService
{
    public function buildRequest(AProviderRequestEntity $providerRequestEntity): void;
}
