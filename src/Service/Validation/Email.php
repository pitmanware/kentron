<?php

namespace Kentron\Service\Validation;

final class Email implements IValidation
{
    private $email = "";

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function isValid(): bool
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) ? true : false;
    }
}
