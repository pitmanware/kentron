<?php

namespace Kentron\Exception;

use \TypeError;

final class CustomTypeError extends TypeError
{
    public function __construct (string $method, string $typeExpected, string $typeSupplied)
    {
        parent::__construct("Argument 1 passed to {$method} must be of the type {$typeExpected}, {$typeSupplied} given.");
    }
}
