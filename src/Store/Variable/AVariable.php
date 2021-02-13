<?php

namespace Kentron\Store\Variable;

use Kentron\Store\IStore;

use Kentron\Service\Assert;
use Kentron\Entity\Template\ACoreCollectionEntity;

abstract class AVariable implements IStore
{
    use TLocalVariables;

    public const ENV_DEV = 1;
    public const ENV_UAT = 2;
    public const ENV_LIVE = 3;

    private const DEFAULT_CIPHER = "AES-256-OFB";

    /**
     * The environment
     * @var null|int
     */
    private static $environment = null;

    /**
    * The encryption cipher
    * @var string
    */
    private static $cipher = self::DEFAULT_CIPHER;

    /**
     * The base64 decoded random byte string initialisation vector to be used on encryption/decryption
     * @var string
     */
    private static $initialisationVector = "";

    /**
     * The database key to be used on encryption/decryption
     * @var string
     */
    private static $databaseKey = "";

    /**
     * All encrypted variables
     * @var array
     */
    private static $encrypted = [];

    /**
     * All decrypted variables
     * @var array
     */
    private static $decrypted = [];

    /**
     * Sets the cipher
     *
     * @param string $cipher
     *
     * @return void
     */
    public static function setCipher(string $cipher): void
    {
        self::$cipher = $cipher;
    }

    /**
     * Sets the environment to dev, uat or live
     *
     * @param integer $environment
     *
     * @return void
     */
    public static function setEnvironment(int $environment): void
    {
        self::$environment = $environment;
    }

    /**
     * Init function
     *
     * Builds the array of system variables
     * @param string $databaseKey The database key from the config
     */
    public static function build(ACoreCollectionEntity $variableDBCollectionEntity, string $databaseKey): void
    {
        self::$databaseKey = $databaseKey;

        if ($variableDBCollectionEntity->countEntities() === 0)
        {
            throw new \InvalidArgumentException("Variable collection entity is empty");
        }

        self::getVariables($variableDBCollectionEntity);
    }

    /**
     * Set a store value. Should be used sparingly out of scope
     *
     * @param int   $index The index of the store to change
     * @param mixed $value The value of that store item
     */
    public static function set(int $index, $value): void
    {
        self::$decrypted[$index] = $value;
    }

    /**
     * Get a system variable
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function get(int $variableID)
    {
        if (!isset(self::$decrypted[$variableID]))
        {
            if (!isset(self::$encrypted[$variableID]))
            {
                // If a variable is not in the decrypted nor the encrypted store
                throw new \Exception("Variable with ID '$variableID' does not exist");
            }

            // Decrypt the found variable in the encrypted store and cast it
            self::$decrypted[$variableID] = self::cast(
                self::decrypt(self::$encrypted[$variableID]->value),
                self::$encrypted[$variableID]->type
            );
        }

        return self::$decrypted[$variableID];
    }

    /**
     * Helpers
     */

    /**
     * Decrypts a value from the database
     *
     * @param  string $toDecrypt The encrypted variable
     *
     * @return string
     */
    public static function encrypt(string $toDecrypt): string
    {
        return openssl_encrypt($toDecrypt, self::$cipher, self::$databaseKey, 0, self::$initialisationVector);
    }

    /**
     * Decrypts a value from the database
     *
     * @param  string $toDecrypt The encrypted variable
     *
     * @return string
     */
    public static function decrypt(string $toDecrypt): string
    {
        return openssl_decrypt($toDecrypt, self::$cipher, self::$databaseKey, 0, self::$initialisationVector);
    }

    /**
     * Returns true if the environment is set to development
     *
     * @return boolean
     */
    public static function onDev(): bool
    {
        return Assert::same(self::$environment, self::ENV_DEV);
    }

    /**
     * Returns true if the environment is set to UAT
     *
     * @return boolean
     */
    public static function onUAT(): bool
    {
        return Assert::same(self::$environment, self::ENV_UAT);
    }

    /**
     * Returns true if the environment is set to live
     *
     * @return boolean
     */
    public static function onLive(): bool
    {
        return Assert::same(self::$environment, self::ENV_LIVE);
    }

    /**
     * Get the system variables from the database
     *
     * @return void
     */
    private static function getVariables(ACoreCollectionEntity $variableDBCollectionEntity): void
    {
        $variableDBEntity = $variableDBCollectionEntity->shiftCoreEntity();

        self::$initialisationVector = base64_decode($variableDBEntity->getValue());

        foreach ($variableDBCollectionEntity->iterateCoreEntities() as $variableDBEntity)
        {
            if ($variableDBEntity->getEncrypted())
            {
                // Save to the encrypted store to be decrypted when needed
                self::$encrypted[$variableDBEntity->getID()] = (object) [
                    "value" => $variableDBEntity->getValue(),
                    "type"  => $variableDBEntity->getType()
                ];
            }
            else
            {
                // If it does not need to be decrypted, cast and save to the decrypted store
                self::$decrypted[$variableDBEntity->getID()] = self::cast(
                    $variableDBEntity->getValue(),
                    $variableDBEntity->getType()
                );
            }

        }
    }

    /**
     * Cast a system variable to its identified type
     * @param  mixed  $value The system var to be casted
     * @param  string $type  The variable type to be case to
     * @return mixed
     */
    private static function cast($value, string $type)
    {
        switch ($type)
        {
            case "bool":
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;

            case "float":
                return (float) $value;
                break;

            case "int":
                return (int) $value;
                break;

            case "array":
                return json_decode($value, true);
                break;

            case "object":
                return json_decode($value, false);
                break;

            case "string":
            default:
                return (string) $value;
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function reset(): void
    {
        self::$environment = null;
        self::$cipher = self::DEFAULT_CIPHER;
        self::$initialisationVector = "";
        self::$databaseKey = "";
        self::$encrypted = [];
        self::$decrypted = [];

        self::resetLocal();
    }
}
