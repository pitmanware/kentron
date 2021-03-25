<?php

namespace Kentron\Service\Provider\Template;

interface IProviderResponseService
{
    public function formatResponse(AProviderEntity $providerEntity): ?array;
}
