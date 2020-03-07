<?php

namespace Kentron\Entity\Template;

use Kentron\Service\DT;

abstract class AMapEntity extends AEntity
{
    protected $ID;
    protected $dateCreated;
    protected $dateUpdated;
    protected $dateDeleted;

    /**
     * Getters
     */

    public function getID (): ?int
    {
        return $this->ID;
    }

    public function getDateCreated (): DT
    {
        return $this->dateCreated;
    }

    public function getDateUpdated (): ?DT
    {
        return $this->dateUpdated;
    }

    public function getDateDeleted (): ?DT
    {
        return $this->dateDeleted;
    }

    /**
     * Setters
     */

    public function setID (int $ID): void
    {
        $this->ID = $ID;
    }

    public function setDateCreated (string $dateCreated): void
    {
        $this->dateCreated = DT::then($dateCreated);
    }

    public function setDateUpdated (?string $dateUpdated): void
    {
        $this->dateUpdated = is_string($dateUpdated) ? DT::then($dateUpdated) : null;
    }

    public function setDateDeleted (?string $dateDeleted): void
    {
        $this->dateDeleted = is_string($dateDeleted) ? DT::then($dateDeleted) : null;
    }
}
