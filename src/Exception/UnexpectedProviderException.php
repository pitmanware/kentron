<?php
declare(strict_types=1);

namespace Kentron\Throwable;

use UnexpectedValueException;

class UnexpectedProviderException extends UnexpectedValueException
{
    public function __construct(string $providerName)
    {
        parent::__construct("Unexpected provider: '$providerName' given.");
    }
}
