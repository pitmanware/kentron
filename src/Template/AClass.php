<?php
declare(strict_types=1);

namespace Kentron\Template;

use \Error;
use \ReflectionClass;

abstract class AClass
{
    public function hasTrait(string|object $trait): bool
    {
        if (is_object($trait)) {
            $trait = $trait::class;
        }
        if (!trait_exists($trait)) {
            throw new Error("Trait '$trait' does not exist");
        }

        return in_array($trait, (new ReflectionClass($this))->getTraitNames());
    }
}
