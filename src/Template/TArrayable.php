<?php

    namespace Kentron\Template;

    /**
     * Convert attributes of an object to an array.
     */
    trait TArrayable
    {
        /**
         * Returns the attributes of self as an array.
         * @return array
         */
        public function toArray (): array
        {
            return object_get_vars($this);
        }
    }
