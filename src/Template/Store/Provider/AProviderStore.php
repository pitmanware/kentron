<?php
declare(strict_types=1);

namespace Kentron\Template\Store\Provider;

use Kentron\Template\Provider\Entity\IProviderEntity;
use Kentron\Template\Store\Variable\Provider\AProviderVarStore;

abstract class AProviderStore
{
    public static AProviderVarStore|string $store;

    /**
     * Sets a provider by the ID
     *
     * @param IProviderEntity $providerEntity
     *
     * @return AProviderVarStore The built provider var store
     */
    public static function set(IProviderEntity $providerEntity): AProviderVarStore|string
    {
        AProviderVarStore::$id = $providerEntity->getID();
        AProviderVarStore::$displayName = $providerEntity->getDisplayName();
        AProviderVarStore::$constantName = $providerEntity->getConstantName();

        static::$store = static::factoryProviderVar();
        static::$store::build();

        return static::$store;
    }

    /**
     * Set the default provider if one has been set as such
     *
     * @return AProviderVarStore The built provider var store
     */
    public static function setDefault(): AProviderVarStore|string
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
     * Returns the static factoried child of AProviderVarStore
     *
     * @return AProviderVarStore
     */
    abstract protected static function factoryProviderVar(): AProviderVarStore|string;
}
