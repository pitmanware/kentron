<?php

    namespace Kentron\Template\Entity;

    use Kentron\Entity\Entity;
    use Kentron\Template\Entity\ACoreEntity;

    abstract class ADBEntity extends ACoreEntity
    {
        /**
         * Name of the table primary column
         * @var string|null
         */
        protected $primaryIDColumn;

        /**
         * Name of the table's created datetime column
         * @var string|null
         */
        protected $dateCreatedColumn;

        /**
         * Name of the table's updated datetime column
         * @var string|null
         */
        protected $dateUpdateColumn;

        /**
         * Name of the table's deleted datetime column
         * @var string|null
         */
        protected $dateDeletedColumn;

        public function __construct (Entity $entity)
        {
            parent::__construct($entity);

            $this->setDefaultTableColumnMap();
        }

        /**
         * Getters
         */

        final public function getDateCreatedColumn (): ?string
        {
            return $this->dateCreatedColumn;
        }

        final public function getDateUpdatedColumn (): ?string
        {
            return $this->dateUpdatedColumn;
        }

        final public function getDateDeletedColumn (): ?string
        {
            return $this->dateDeletedColumn;
        }

        /**
         * Generator to loop through the available properties specific to builing the table
         * @param  boolean  $allowNullable Allows null values to be returned
         * @return iterable
         */
        public function iterateAvailableProperties (bool $allowNullable = false): iterable
        {
            foreach ($this->iterateProperties($allowNullable) as $property => $value) {
                if (is_null($value) && !$allowNullable) {
                    continue;
                }
                if ($value instanceof \DateTime) {
                    $value = $value->format("Y-m-d H:i:s");
                }

                yield $property => $value;
            }
        }

        /**
         * Overridden method to get the input data property if it exists
         * @param  array  $data     The input data from the table
         * @param  string $property An expected column name
         * @return mixed            Null if doesn't exist
         */
        final protected function getDataProperty ($data, string $property)
        {
            if (is_array($data) && isset($data[$property])) {
                return $data[$property];
            }

            return null;
        }

        /**
         * Sets default getters and setters for common table columns
         * @return void
         */
        final private function setDefaultTableColumnMap (): void
        {
            if (
                isset($this->primaryIDColumn) &&
                $this->isValidMethod($this->coreEntity, "getID") &&
                $this->isValidMethod($this->coreEntity, "setID")
            ) {
                $this->propertyMap[$this->primaryIDColumn] = [
                    "get" => "getID",
                    "set" => "setID"
                ];
            }

            if (
                isset($this->dateCreatedColumn) &&
                $this->isValidMethod($this->coreEntity, "getDateCreated") &&
                $this->isValidMethod($this->coreEntity, "setDateCreated")
            ) {
                $this->propertyMap[$this->dateCreatedColumn] = [
                    "get" => "getDateCreated",
                    "set" => "setDateCreated"
                ];
            }

            if (
                isset($this->dateUpdateColumn) &&
                $this->isValidMethod($this->coreEntity, "getDateUpdated") &&
                $this->isValidMethod($this->coreEntity, "setDateUpdated")
            ) {
                $this->propertyMap[$this->dateUpdateColumn] = [
                    "get" => "getDateUpdated",
                    "set" => "setDateUpdated"
                ];
            }

            if (
                isset($this->dateDeletedColumn) &&
                $this->isValidMethod($this->coreEntity, "getDateDeleted") &&
                $this->isValidMethod($this->coreEntity, "setDateDeleted")
            ) {
                $this->propertyMap[$this->dateDeletedColumn] = [
                    "get" => "getDateDeleted",
                    "set" => "setDateDeleted"
                ];
            }

        }
    }
