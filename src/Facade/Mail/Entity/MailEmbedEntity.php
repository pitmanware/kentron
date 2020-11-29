<?php

namespace Kentron\Facade\Mail\Entity;

final class MailEmbedEntity extends AMailEntity
{
    private $cid;

    /**
     * Getters
     */

    public function getCid (): ?string
    {
        return $this->cid;
    }

    /**
     * Setters
     */

    public function setCid (string $cid): void
    {
        $this->cid = $cid;
    }
}
