<?php

namespace Kentron\Template;

interface IStore
{
    /**
     * Enforces that a static store can be reset to an original state
     *
     * @return void
     */
    public static function reset(): void;
}
