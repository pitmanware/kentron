<?php

namespace Kentron\Service\Provider;

use Kentron\Store\Variable\AVariable;
use Kentron\Throwable\UnexpectedProviderException;

// Services
use Kentron\Service\Provider\Template\AProviderFactory;

final class ProviderFactory
{
    /**
     * Get the provider service based on the ID given in the route
     * @return AProviderFactory
     * @throws UnexpectedProviderException
     */
    public static function getProvider(): AProviderFactory
    {
        $providerClass = AVariable::getProviderClass();

        if (!class_exists($providerClass)) {
            throw new UnexpectedProviderException($providerClass);
        }

        return new $providerClass;
    }
}
