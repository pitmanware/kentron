<?php
declare(strict_types=1);

namespace Kentron\Template\Store;

use Kentron\Template\AClass;

abstract class AStore extends AClass
{
    /**
     * Enforces that a static store can be reset to an original state
     *
     * @return void
     */
    abstract public static function reset(bool $hard = false): void;
}
