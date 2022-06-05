<?php
declare(strict_types=1);

namespace Kentron\Template\Alert;

use Kentron\Support\Type\Type;
use \ReflectionClass;

/**
 * ErrorCode handling methods
 */
trait TErrorCode
{
    /** The bitwise error codes */
    private int $errorCodes = 0;

    /**
     * Add an error code
     *
     * @param int $errorCode
     *
     * @return void
     */
    final public function addErrorCode(int $errorCode): void
    {
        $this->errorCodes |= $errorCode;
    }

    /**
     * Checks if an error code has been set
     *
     * @param int $errorCode
     *
     * @return bool
     */
    final public function hasErrorCode(int $errorCode): bool
    {
        return !!($this->errorCodes & $errorCode);
    }

    /**
     * Checks if any error codes have been set
     *
     * @return bool
     */
    final public function hasErrorCodes(): bool
    {
        return $this->errorCodes !== 0;
    }

    /**
     * Returns the bitwise error codes
     *
     * @return int
     */
    final public function getErrorCodes(): int
    {
        return $this->errorCodes;
    }

    /**
     * Reset the error codes to 0
     *
     * @return void
     */
    final public function resetErrorCodes(): void
    {
        $this->errorCodes = 0;
    }

    /**
     * Merge the error codes from $alert into self
     *
     * @param AAlert|TErrorCode $alert
     *
     * @return void
     */
    final public function mergeErrorCodes(mixed $alert): void
    {
        if (
            $alert instanceof AAlert ||
            (Type::isObject($alert) && (in_array(self::class, (new ReflectionClass($alert))->getTraitNames())))
        ) {
            /** @var self $alert */
            $this->errorCodes = $alert->getErrorCodes();
        }
    }
}
