<?php

namespace Kentron\Service\Provider\Template;

interface IProviderResponse
{
    public function formatResponse(AProviderEntity $providerEntity): ?array;
}
