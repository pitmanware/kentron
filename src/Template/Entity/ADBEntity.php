<?php

namespace Kentron\Template\Entity;

use Kentron\Template\Entity\{ACoreEntity, AMapEntity};

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

    public function __construct (AMapEntity $mapEntity)
    {
        parent::__construct($mapEntity);

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
            if (
                (is_null($value) && !$allowNullable) ||
                (is_object($value) && is_subclass_of($value, AEntity::class))
            ) {
                // Don't return entities
                continue;
            }

            if ($value instanceof \DateTime) {
                $value = $value->format("Y-m-d H:i:s");
            }

            yield $property => $value;
        }
    }

    /**
     * Sets default getters and setters for common table columns
     * @return void
     */
    final private function setDefaultTableColumnMap (): void
    {
        if (
            isset($this->primaryIDColumn) &&
            $this->getRootEntity()->isValidMethod("getID") &&
            $this->getRootEntity()->isValidMethod("setID")
        ) {
            $this->propertyMap[$this->primaryIDColumn] = [
                "get" => "getID",
                "set" => "setID"
            ];
        }

        if (
            isset($this->dateCreatedColumn) &&
            $this->getRootEntity()->isValidMethod("getDateCreated") &&
            $this->getRootEntity()->isValidMethod("setDateCreated")
        ) {
            $this->propertyMap[$this->dateCreatedColumn] = [
                "get" => "getDateCreated",
                "set" => "setDateCreated"
            ];
        }

        if (
            isset($this->dateUpdateColumn) &&
            $this->getRootEntity()->isValidMethod("getDateUpdated") &&
            $this->getRootEntity()->isValidMethod("setDateUpdated")
        ) {
            $this->propertyMap[$this->dateUpdateColumn] = [
                "get" => "getDateUpdated",
                "set" => "setDateUpdated"
            ];
        }

        if (
            isset($this->dateDeletedColumn) &&
            $this->getRootEntity()->isValidMethod("getDateDeleted") &&
            $this->getRootEntity()->isValidMethod("setDateDeleted")
        ) {
            $this->propertyMap[$this->dateDeletedColumn] = [
                "get" => "getDateDeleted",
                "set" => "setDateDeleted"
            ];
        }

    }
}
