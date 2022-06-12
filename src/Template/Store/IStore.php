<?php
declare(strict_types=1);

namespace Kentron\Template\Store;

interface IStore
{
    /**
     * Enforces that a static store can be reset to an original state
     *
     * @return void
     */
    public static function reset(bool $hard = false): void;
}
