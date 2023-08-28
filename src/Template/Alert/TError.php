<?php
declare(strict_types=1);

namespace Kentron\Template\Alert;

use Kentron\Enum\EType;
use Kentron\Support\Type\Type;

use \ReflectionClass;

/**
 * Error handling methods
 */
trait TError
{
    /** Any errors that need to contained go here */
    public array $errors = [];

    /**
     * Add one or an array of errors
     *
     * @param string|string[] $errors
     */
    final public function addError(string|array $errors): void
    {
        if (is_string($errors)) {
            $this->errors[] = $errors;
        }
        else if (is_array($errors) && !empty($errors)) {
            if (Type::isAssoc($errors) || !Type::of($errors)->isArrayOf(EType::TYPE_STRING)) {
                throw new \UnexpectedValueException("Cannot add errors of type " . Type::get($errors));
            }
            $this->errors = array_merge($this->errors, $errors);
        }
    }

    /**
     * Merge the errors from $alert into self
     *
     * @param AAlert|TError $alert
     *
     * @return void
     */
    final public function mergeErrors(mixed $alert): void
    {
        if (
            $alert instanceof AAlert ||
            (Type::isObject($alert) && (in_array(self::class, (new ReflectionClass($alert))->getTraitNames())))
        ) {
            /** @var self $alert */
            $this->addError($alert->errors);
        }
    }

    /**
     * Checks if any errors have been added to the array
     *
     * @return boolean
     */
    final public function hasErrors(): bool
    {
        return (count($this->errors) > 0);
    }

    /**
     * Empty the array of errors
     *
     * @return void
     */
    final public function resetErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Return the errors with a key
     *
     * @return array<string,string[]>
     */
    final public function normaliseErrors(): array
    {
        return [
            "errors" => $this->errors
        ];
    }
}
