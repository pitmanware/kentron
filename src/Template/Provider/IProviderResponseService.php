<?php

namespace Kentron\Template\Provider;

use Kentron\Template\Provider\Entity\AProviderEntity;

interface IProviderResponseService
{
    public function formatResponse(AProviderEntity $providerEntity): ?array;
}
