<?php

    namespace Kentron\Entity;

    use Kentron\Template\{
        AError,
        IGetTableProperties
        ISetTableProperties,
    };

    /**
     * Base class for storing table data, translating records and error handling
     */
    class Entity extends AError implements IGetTableProperties, ISetTableProperties
    {
        /**
         * Column => property map for table data
         * @var array $tableProperties
         */
        protected $tableProperties = [];

        public function getTableProperties (?bool $allowNull = false): array
        {
            $properties = [];

            foreach ($this->tableProperties as $column => $property) {
                if (property_exists($this, $property)) {
                    if (is_null($this->$property) && !$allowNull) {
                        continue;
                    }
                    $properties[$column] = $this->$property;
                }
            }

            return $properties;
        }

        public function setTableProperties (array $properties, ?bool $allowNull = false): void
        {
            foreach ($this->tableProperties as $column => $property) {
                if (is_null($properties[$column]) && !$allowNull) {
                    continue;
                }
                $this->$property = $properties[$column];
            }
        }
    }
