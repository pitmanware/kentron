<?php

namespace Kentron\Template\Provider\Service;

use Kentron\Template\Provider\Entity\AProviderRequestEntity;

interface IProviderResponseService
{
    public function formatResponse(AProviderRequestEntity $providerRequestEntity): ?array;
}
