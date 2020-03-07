<?php

namespace Kentron\Service\Validation;

use Brick\Postcode\PostcodeFormatter;

use Kentron\Template\IValidation;

final class Postcode implements IValidation
{
    private $countryCode;
    private $postcode;

    public function __construct (string $postcode, string $countryCode = "GB")
    {
        $this->postcode = $postcode;
        $this->countryCode = $countryCode;
    }

    public function isValid (): bool
    {
        $formatter = new PostcodeFormatter();

        try
        {
            $formatter->format($this->countryCode, $this->postcode);
        }
        catch (\Exception $ex)
        {
            return false;
        }

        return true;
    }
}
