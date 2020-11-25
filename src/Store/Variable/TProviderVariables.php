<?php

namespace Kentron\Store\Variable;

trait TProviderVariables
{
    /**
     * The system_var ID for provder details; defaults to 3.
     * Expected to be overridden
     * @var integer
     */
    protected static $providerVariableID = 3;

    /**
     * The chosen provider ID
     * @var int
     */
    private static $providerID;

    /**
     * The provider factory class
     * @var string
     */
    private static $providerClass;

    /**
     * The chosen provider name
     * @var string
     */
    private static $providerName;

    /**
     * The chosen provider endpoint URL
     * @var string
     */
    private static $providerUrl;

    /**
     * The chosen provider endpoint username
     * @var string
     */
    private static $providerUsername;

    /**
     * The chosen provider endpoint password
     * @var string
     */
    private static $providerPassword;

    /**
     * Any additional details needed for the provider endpoint
     * @var object|null
     */
    private static $providerExtraDetails = null;

    /**
     * Setters
     */

    /**
     * Sets a provider by the ID
     * @param int $providerID
     * @throws \ErrorException If the given ID does not match any providers
     */
    public static function setProvider (int $providerID): void
    {
        $provider = self::get(self::$providerVariableID)->{$providerID} ?? null;

        if (is_null($provider)) {
            throw new \ErrorException("Provider with ID $providerID does not exist");
        }

        // TODO turn provider into a map entity
        self::$providerID       = $providerID;
        self::$providerClass    = $provider->class;
        self::$providerName     = $provider->name;
        self::$providerUrl      = self::get($provider->url);
        self::$providerUsername = self::get($provider->username);
        self::$providerPassword = self::get($provider->password);

        if (isset($provider->extra) && is_object($provider->extra)) {
            self::$providerExtraDetails = self::extractExtraDetails($provider->extra);
        }
    }

    public static function setDefaultProvider (): void
    {
        self::setProvider(self::get(4));
    }

    /**
     * Getters
     */

    public static function getProviderID (): int
    {
        return self::$providerID;
    }

    public static function getProviderClass (): string
    {
        return self::$providerClass;
    }

    public static function getProviderName (): string
    {
        return self::$providerName;
    }

    public static function getProviderUsername (): string
    {
        return self::$providerUsername;
    }

    public static function getProviderPassword (): string
    {
        return self::$providerPassword;
    }

    public static function getProviderUrl (): string
    {
        return self::$providerUrl;
    }

    public static function getProviderExtraDetails (): ?object
    {
        return self::$providerExtraDetails;
    }

    /**
     * Helpers
     */

    /**
     * For if the provider has any additional details for connecting to the endpoint
     * @param  object $extraDetails
     * @return object
     */
    private static function extractExtraDetails (object $extraDetails): object
    {
        $details = [];

        foreach (get_object_vars($extraDetails) as $key => $value) {
            if (is_object($value)) {
                $details[$key] = self::extractExtraDetails($value);
            }
            else if (is_int($value)) {
                $details[$key] = self::get($value);
            }
        }

        return (object) $details;
    }
}
