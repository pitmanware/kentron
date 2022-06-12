<?php
declare(strict_types=1);

namespace Kentron\Template\Alert;

use Kentron\Template\Alert\AAlert;
use Kentron\Support\Type\Type;
use Kentron\Struct\SType;

use \ReflectionClass;

/**
 * Warning handling methods
 */
trait TWarning
{
    /** Any warnings that need to contained go here */
    public array $warnings = [];

    /**
     * Add one or an array of warnings
     *
     * @param string|string[] $warnings
     */
    final public function addWarning(string|array $warnings): void
    {
        if (is_string($warnings)) {
            $this->warnings[] = $warnings;
        }
        else if (is_array($warnings)) {
            if (Type::isAssoc($warnings) || !Type::of($warnings)->isArrayOf(SType::TYPE_STRING)) {
                throw new \UnexpectedValueException("Cannot add warnings of type " . Type::get($warnings));
            }
            $this->warnings = array_merge($this->warnings, $warnings);
        }
    }

    /**
     * Merge the warnings from $alert into self
     *
     * @param AAlert|TWarning $alert
     *
     * @return void
     */
    final public function mergeWarnings(mixed $alert): void
    {
        if (
            $alert instanceof AAlert ||
            (Type::isObject($alert) && (in_array(self::class, (new ReflectionClass($alert))->getTraitNames())))
        ) {
            /** @var self $alert */
            $this->addWarning($alert->warnings);
        }
    }

    /**
     * Checks if any warnings have been added to the array
     *
     * @return bool
     */
    final public function hasWarnings(): bool
    {
        return (count($this->warnings) > 0);
    }

    /**
     * Empty the array of warnings
     *
     * @return void
     */
    final public function resetWarnings(): void
    {
        $this->warnings = [];
    }

    /**
     * Return the warnings with a key
     *
     * @return array<string,string[]>
     */
    final public function normaliseWarnings(): array
    {
        return [
            "warnings" => $this->warnings
        ];
    }
}
