<?php

namespace Kentron\Template\Provider\Service;

use Kentron\Template\Provider\Entity\AProviderEntity;

interface IProviderResponseService
{
    public function formatResponse(AProviderEntity $providerEntity): ?array;
}
