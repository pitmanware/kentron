<?php
declare(strict_types=1);

namespace Kentron\Exception;

use \Error;

final class MethodOverrideError extends Error
{
    public function __construct(string $method)
    {
        parent::__construct("Method {$method} must be overridden");
    }
}
