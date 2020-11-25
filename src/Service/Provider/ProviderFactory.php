<?php

namespace Kentron\Service\Provider;

use Kentron\Store\Variable\AVariable;
use Kentron\Throwable\UnexpectedProviderException;

// Services
use App\Module\Core\Provider\Template\AProviderFactory;

final class ProviderFactory
{
    /**
     * Get the provider service based on the ID given in the route
     * @return AProviderFactory
     * @throws UnexpectedProviderException
     */
    public static function getProvider (): string
    {
        $providerName = AVariable::getProviderName();
        $methodName = "get{$providerName}Factory";

        if (method_exists(__CLASS__, $methodName) && is_callable([__CLASS__, $methodName])) {
            return self::$methodName()::init();
        }

        throw new UnexpectedProviderException($providerName);
    }
}
