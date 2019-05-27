<?php

    namespace Kentron\Proxy\Validation;

    use libphonenumber\{PhoneNumberUtil, PhoneNumberFormat};

    use Kentron\Template\IValidation;

    class PhoneNumber implements IValidation
    {
        public  $country        = "",
                $number         = "",
                $validValue     = true;

        private $phoneUtil      = null,
                $phoneObject    = null;

        public function __construct (string $phoneNumber, string $country = "GB")
        {
            $this->number   = str_replace(" ", "", $phoneNumber);
            $this->country  = $country;

            try {
                $this->phoneUtil = PhoneNumberUtil::getInstance();
            }
            catch (\Exception $err) {
                $this->validValue = false;
            }
        }

        public function isValid (): bool
        {
            if (!$this->validValue) {
                return false;
            }

            $this->phoneObject = $this->phoneUtil->parse($this->number, $this->country);

            return $this->phoneUtil->isValidNumber($this->phoneObject);
        }

        /**
         *
         *  Default to National
         *
         */
        public function getFormat (): string
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
