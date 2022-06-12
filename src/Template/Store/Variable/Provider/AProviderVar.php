<?php
declare(strict_types=1);

namespace Kentron\Template\Store\Variable\Provider;

use Kentron\Template\Store\IJwtStore;
use Kentron\Template\Store\Variable\AVariable;
use Kentron\Template\Store\Variable\IVariableDbEntity;

use \ReflectionClass;

abstract class AProviderVar extends AVariable
{
    /** Used for the factory */
    public const NAME = "";

    /** The chosen provider ID */
    public static int $id;
    /** The name used for error messages and the like */
    public static string $displayName = "";
    /** The constant name used for the factory */
    public static string $constantName = "";

    /**
     * Protected methods
     */

    /**
     * Load all provider variables
     *
     * @return void
     */
    protected static function load(): void
    {
        $providerVariableDbEntities = static::getProviderVariableDbEntities();

        foreach ($providerVariableDbEntities as $providerVariableDbEntity) {
            parent::loadVariable($providerVariableDbEntity);
        }
    }

    /** @return IVariableDbEntity[] */
    abstract protected static function getProviderVariableDbEntities(): array;

    /**
     * Helpers
     */

    public static function usesJwt(): bool
    {
        return (new ReflectionClass(static::class))->implementsInterface(IJwtStore::class);
    }
}
