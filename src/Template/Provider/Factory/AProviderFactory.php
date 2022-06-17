<?php

namespace Kentron\Template\Provider\Factory;

use Kentron\Template\Provider\Service\AProviderService;

abstract class AProviderFactory
{
    protected function __construct(
        protected AProviderService $service
    ) {}
}
