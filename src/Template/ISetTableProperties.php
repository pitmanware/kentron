<?php

    namespace Kentron\Template;

    interface ISetTableProperties
    {

        /**
         * Set the properties of the implemented class
         * @param array     $properties These come directly from the associated table
         * @param null|bool $allowNull  If false and column is null the property will not be set
         * @return void
         */
        public function setTableProperties (array $properties, ?bool $allowNull = false): void;
    }
