<?php

    namespace Kentron\Template;

    interface IGetTableProperties
    {
        /**
         * Get the properties of the implemented class for injection into the associated table
         * @param null|bool $allowNull If false, null fields will not be returned
         * @return array The previously applied table properties
         */
        public function getTableProperties (?bool $allowNull = false): array;
    }
