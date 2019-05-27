<?php

    namespace Kentron\Proxy\Validation;

    use Kentron\Template\IValidation;

    class Date implements IValidation
    {
        private $date   = null,
                $format = null;

        public function __construct (string $date, ?string $format = null)
        {
            $this->date     = $date;
            $this->format   = $format;
        }

        public function isValid (): bool
        {
            try {
                if (!is_null($this->format)) {
                    \DateTime::createFromFormat($this->date, $this->format);
                }
                else {
                    new \DateTime($this->date);
                }
            }
            catch (\Exception $ex) {
                return false;
            }

            return true;
        }
    }
