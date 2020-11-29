<?php

namespace Kentron\Facade\Mail\Entity;

use Kentron\Entity\Template\AEntity;
use Kentron\Service\File;

final class MailTargetEntity extends AEntity
{
    /**
     * The recipient's email
     *
     * @var string
     */
    private $email;

    /**
     * The recipient's name
     *
     * @var string
     */
    private $name;

    public function __construct (?string $email = null, ?string $name = null)
    {
        $this->email = $email ?? $this->email;
        $this->name = $name ?? $this->name;
    }

    /**
     * Getters
     */

    public function getEmail (): string
    {
        return $this->email;
    }

    public function getName (): ?string
    {
        return $this->name;
    }

    /**
     * Setters
     */

    final public function setEmail (string $email): void
    {
        $this->email = $email;
    }

    final public function setName (?string $name = null): void
    {
        $this->name = $name;
    }
}
