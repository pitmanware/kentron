<?php

    namespace Kentron\Proxy\Validation;

    use Kentron\Template\IValidation;

    class Email implements IValidation
    {
        private $email = "";

        public function __construct (string $email)
        {
            $this->email = $email;
        }

        public function isValid (): bool
        {
            return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
        }
    }
