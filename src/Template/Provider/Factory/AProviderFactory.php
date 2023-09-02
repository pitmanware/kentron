<?php

namespace Kentron\Template\Provider\Factory;

use Kentron\Template\AClass;
use Kentron\Template\Provider\Service\AProviderService;

abstract class AProviderFactory extends AClass
{
    protected function __construct(
        protected AProviderService $service
    ) {}
}
