<?php
declare(strict_types=1);

namespace Kentron\Template\Store\Variable;

use Kentron\Support\Type\Type;
use Kentron\Support\System\Crypt;

use Kentron\Template\Store\IStore;
use Kentron\Template\Store\Variable\IVariableDbEntity;

use \Exception;

abstract class AVariableStore implements IStore
{
    /** All decrypted variables */
    protected static array $store = [];

    /**
     * Init function. Builds the array of system variables
     *
     * @return void
     */
    final public static function build(): void
    {
        static::load();
    }

    /**
     * Get a system variable
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws Exception
     */
    final public static function get(string $key): mixed
    {
        if (!isset(self::$store[$key])) {
            // If a variable is not in the store
            throw new Exception("Variable with key '$key' does not exist");
        }

        return self::$store[$key];
    }

    public static function reset(bool $hard = false): void {}

    /**
     * Helpers
     */

    /**
     * Get the system variables from the database
     *
     * @param IVariableDbEntity $variableDbEntity
     *
     * @return void
     */
    final protected static function loadVariable(IVariableDbEntity $variableDbEntity): void
    {
        $value = $variableDbEntity->getValue();
        if ($variableDbEntity->isEncrypted()) {
            $value = Crypt::decrypt($variableDbEntity->getValue());
        }

        self::$store[$variableDbEntity->getConstantName()] = Type::cast($value)->to($variableDbEntity->getType());
    }

    protected static function load(): void {}
}
