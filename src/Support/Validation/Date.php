<?php
declare(strict_types=1);

namespace Kentron\Support\Validation;

use \DateTime;
use \Exception;

final class Date implements IValidation
{
    public function __construct(
        private string $date,
        private string|null $format = null
    ) {}

    public function isValid(): bool
    {
        try {
            if (isset($this->format)) {
                DateTime::createFromFormat($this->date, $this->format);
            }
            else {
                new DateTime($this->date);
            }
        }
        catch (Exception $ex) {
            return false;
        }

        return true;
    }
}
