<?php

namespace Kentron\Service\Provider\Template;

interface IProviderRequest
{
    public function buildRequest (AProviderEntity $providerEntity): void;
}
