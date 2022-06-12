<?php
declare(strict_types=1);

namespace Kentron\Template\Store\Provider;

use Kentron\Template\Store\Variable\Provider\AProviderVar;

abstract class AProvider
{
    public static AProviderVar|string $store;

    /**
     * Sets a provider by the ID
     *
     * @param IProviderEntity $providerEntity
     *
     * @return AProviderVar The built provider var store
     */
    public static function set(IProviderEntity $providerEntity): AProviderVar|string
    {
        AProviderVar::$id = $providerEntity->getID();
        AProviderVar::$displayName = $providerEntity->getDisplayName();
        AProviderVar::$constantName = $providerEntity->getConstantName();

        static::$store = static::factoryProviderVar();
        static::$store::build();

        return static::$store;
    }

    /**
     * Set the default provider if one has been set as such
     *
     * @return AProviderVar The built provider var store
     */
    public static function setDefault(): AProviderVar|string
    {
        return self::set(static::getDefaultProviderEntity());
    }

    /**
     * Protected methods
     */

    /**
     * Get the default provider from the DB
     *
     * @return IProviderEntity
     */
    abstract protected static function getDefaultProviderEntity(): IProviderEntity;

    /**
     * Returns the static factoried child of AProviderVar
     *
     * @return AProviderVar
     */
    abstract protected static function factoryProviderVar(): AProviderVar|string;
}
