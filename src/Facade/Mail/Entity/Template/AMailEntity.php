<?php

namespace Kentron\Facade\Mail\Entity\Template;

use Kentron\Entity\Template\AEntity;

abstract class AMailEntity extends AEntity
{
    private $path;
    private $name;

    /**
     * Getters
     */

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Setters
     */

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
