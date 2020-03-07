<?php

namespace Kentron\Service\Validation;

use libphonenumber\{PhoneNumberUtil, PhoneNumberFormat};

use Kentron\Template\IValidation;

final class PhoneNumber implements IValidation
{
    private $country = "GB";
    private $number;
    private $phoneUtil;
    private $phoneObject;

    public function __construct (string $phoneNumber)
    {
        $this->number = str_replace(" ", "", $phoneNumber);
    }

    public function isValid (): bool
    {
        try {
            $this->phoneUtil = PhoneNumberUtil::getInstance();
        }
        catch (\Exception $ex) {
            return false;
        }

        $this->phoneObject = $this->phoneUtil->parse($this->number, $this->country);

        return $this->phoneUtil->isValidNumber($this->phoneObject);
    }

    public function getDefaultFormat (): string
    {
        return $this->getNationalFormat();
    }

    public function getNationalFormat (): string
    {
        return $this->phoneUtil->format($this->phoneObject, PhoneNumberFormat::NATIONAL);
    }

    public function getInternationalFormat (): string
    {
        return $this->phoneUtil->format($this->phoneObject, PhoneNumberFormat::INTERNATIONAL);
    }

    public function getE164Format (): string
    {
        return $this->phoneUtil->format($this->phoneObject, PhoneNumberFormat::E164);
    }
}
