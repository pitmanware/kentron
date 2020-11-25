<?php

namespace Kentron\Throwable;

class UnexpectedProviderException extends \UnexpectedValueException
{
    public function __construct (string $providerName)
    {
        parent::__construct("Unexpected provider: '$providerName' given.");
    }
}
