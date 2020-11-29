<?php

namespace Kentron\Facade\Mail\Entity\Embed;

use Kentron\Facade\Mail\Entity\Template\AMailEntity;

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
