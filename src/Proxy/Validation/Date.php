<?php

    namespace Kentron\Proxy\Validation;

    use Kentron\Template\IValidation;

    final class Date implements IValidation
    {
        private $date;
        private $format;

        public function __construct (string $date, ?string $format = null)
        {
            $this->date = $date;
            $this->format = $format;
        }

        public function isValid (): bool
        {
            try
            {
                if (isset($this->format))
                {
                    \DateTime::createFromFormat($this->date, $this->format);
                }
                else
                {
                    new \DateTime($this->date);
                }
            }
            catch (\Exception $ex)
            {
                return false;
            }

            return true;
        }
    }
