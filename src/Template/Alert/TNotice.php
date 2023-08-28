<?php
declare(strict_types=1);

namespace Kentron\Template\Alert;

use Kentron\Enum\EType;
use Kentron\Template\Alert\AAlert;
use Kentron\Support\Type\Type;

use \ReflectionClass;

/**
 * Notice handling methods
 */
trait TNotice
{
    /** Any notices that need to contained go here */
    public array $notices = [];

    /**
     * Add one or an array of notices
     *
     * @param string|string[] $notices
     */
    final public function addNotice(string|array $notices): void
    {
        if (is_string($notices)) {
            $this->notices[] = $notices;
        }
        else if (is_array($notices)) {
            if (Type::isAssoc($notices) || !Type::of($notices)->isArrayOf(EType::TYPE_STRING)) {
                throw new \UnexpectedValueException("Cannot add notices of type " . Type::get($notices));
            }
            $this->notices = array_merge($this->notices, $notices);
        }
    }

    /**
     * Merge the notices from $alert into self
     *
     * @param AAlert|TNotice $alert
     *
     * @return void
     */
    final public function mergeNotices(mixed $alert): void
    {
        if (
            $alert instanceof AAlert ||
            (Type::isObject($alert) && (in_array(self::class, (new ReflectionClass($alert))->getTraitNames())))
        ) {
            /** @var self $alert */
            $this->addNotice($alert->notices);
        }
    }

    /**
     * Checks if any notices have been added to the array
     *
     * @return bool
     */
    final public function hasNotices(): bool
    {
        return (count($this->notices) > 0);
    }

    /**
     * Empty the array of notices
     *
     * @return void
     */
    final public function resetNotices(): void
    {
        $this->notices = [];
    }

    /**
     * Return the notices with a key
     *
     * @return array<string,string[]>
     */
    final public function normaliseNotices(): array
    {
        return [
            "notices" => $this->notices
        ];
    }
}
