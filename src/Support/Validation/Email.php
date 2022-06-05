<?php
declare(strict_types=1);

namespace Kentron\Support\Validation;

final class Email implements IValidation
{
    public function __construct(
        private string $email
    ) {}

    public function isValid(): bool
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) ? true : false;
    }
}
