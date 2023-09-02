<?php
declare(strict_types=1);

namespace Kentron\Support\Validation;

use Kentron\Support\Validation\IValidation;

final class Postcode implements IValidation
{
    public function __construct (
        private string $postcode,
        private bool $isIrish = false
    )
    {
        $this->postcode = strtoupper(trim(str_replace(' ', '', $postcode)));
    }

    public function isValid (): bool
    {
        if (!$this->isIrish) {
            return
                !!preg_match("/(^[A-Z]{1,2}[0-9R][0-9A-Z]?[\s]?[0-9][ABD-HJLNP-UW-Z]{2}$)/i", $this->postcode) ||
                !!preg_match("/(^[A-Z]{1,2}[0-9R][0-9A-Z]$)/i", $this->postcode)
            ;
        }

        // Insert a space by first removing any spaces then adding one in the fourth position.
        $withspace = substr($this->postcode, 0, 3) . ' ' . substr($this->postcode, 3, 4);

        return
            !!preg_match(pattern: "/^[ACDEFHKNPRTVWXY]{1}[0-9]{2}[ ]{1}[ACDEFHKNPRTVWXY0-9]{4}$/i", subject: $withspace, flags: PREG_OFFSET_CAPTURE) ||
            !!preg_match(pattern: "/^D6W{1}[ ]{1}[ACDEFHKNPRTVWXY0-9]{4}$/i", subject: $withspace, flags: PREG_OFFSET_CAPTURE)
        ;
    }
}
