<?php

    namespace Kentron\Service\Validation;

    use Brick\Postcode\PostcodeFormatter;

    use Kentron\Template\IValidation;

    final class Postcode implements IValidation
    {
        private $countryCode    = "",
                $formatter      = null,
                $postcode       = "";

        public function __construct (string $postcode, string $countryCode = "GB")
        {
            $this->postcode = $postcode;
            $this->countryCode = $countryCode;

            $this->formatter = new PostcodeFormatter();
        }

        public function isValid (): bool
        {
            try {
                $this->formatter->format($this->countryCode, $this->postcode);
            }
            catch (\Exception $ex) {
                return false;
            }

            return true;
        }
    }
