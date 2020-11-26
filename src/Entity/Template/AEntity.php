<?php

namespace Kentron\Entity\Template;

use Kentron\Template\AAlert;

abstract class AEntity extends AAlert
{
    /**
     * Validates a callable method
     *
     * @param string|null $method The method to check
     *
     * @return bool
     */
    final public function isValidMethod (?string $method = null): bool
    {
        if (
            isset($method) &&
            method_exists($this, $method) &&
            is_callable([$this, $method])
        ) {
            return true;
        }

        return false;
    }
}
