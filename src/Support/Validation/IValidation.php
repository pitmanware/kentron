<?php
declare(strict_types=1);

namespace Kentron\Support\Validation;

interface IValidation
{
    public function isValid(): bool;
}
