<?php
declare(strict_types=1);

namespace Kentron\Template\Store\Variable\System;

use Kentron\Support\System\Crypt;
use Kentron\Template\Store\Variable\AVariable;
use Kentron\Template\Store\Variable\IVariableDbEntity;

abstract class ASystemVar extends AVariable
{
    protected const VAR_SYSTEM_ACTIVE = "SYSTEM_ACTIVE";
    protected const VAR_EMAIL_USERNAME = "EMAIL_USERNAME";
    protected const VAR_EMAIL_PASSWORD = "EMAIL_PASSWORD";
    protected const VAR_EMAIL_DEVELOPMENT_ADDRESS = "EMAIL_DEVELOPMENT_ADDRESS";
    protected const VAR_DEFAULT_PROVIDER_ID = "DEFAULT_PROVIDER_ID";

    /**
     * Default getters
     */

    final public static function isSystemActive(): bool
    {
        return parent::get(static::VAR_SYSTEM_ACTIVE);
    }

    final public static function getEmailUsername(): string
    {
        return parent::get(static::VAR_EMAIL_USERNAME);
    }

    final public static function getEmailPassword(): string
    {
        return parent::get(static::VAR_EMAIL_PASSWORD);
    }

    final public static function getEmailDevelopmentAddress(): string
    {
        return parent::get(static::VAR_EMAIL_DEVELOPMENT_ADDRESS);
    }

    final public static function getDefaultProviderId(): int
    {
        return parent::get(static::VAR_DEFAULT_PROVIDER_ID);
    }

    /**
     * Protected methods
     */

    /** @return IVariableDbEntity[] */
    abstract protected static function getSystemVariableDbEntities(): array;

    /**
     * Get and load all system variables from the database
     *
     * @return void
     */
    protected static function load(): void
    {
        $systemVariableDbEntities = static::getSystemVariableDbEntities();

        $systemVariableDbEntity = array_shift($systemVariableDbEntities);

        Crypt::$initialisationVector = base64_decode($systemVariableDbEntity->getValue());

        foreach ($systemVariableDbEntities as $systemVariableDbEntity) {
            parent::loadVariable($systemVariableDbEntity);
        }
    }
}
