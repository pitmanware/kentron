<?php

    namespace Kentron\Template;

    /**
     * Error handling methods
     */
    abstract class AError
    {
        /**
         * The error array
         * @var array
         */
        private $errors = [];

        /**
         * Return the full array of errors
         * @return array
         */
        final public function getErrors (): array
        {
            return $this->errors;
        }

        /**
         * Add one or an array of errors
         * @param string|array $errors
         */
        final public function addError ($errors): void
        {
            if (is_string($errors)) {
                $this->errors[] = $errors;
            }
            else if (is_array($errors)) {
                $this->errors = array_merge($this->errors, $errors);
            }
        }

        /**
         * Checks if any errors have been added to the array
         * @return boolean
         */
        final public function hasErrors (): bool
        {
            return (count($this->errors) > 0);
        }
    }
