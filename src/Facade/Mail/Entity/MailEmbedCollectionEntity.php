<?php

namespace Kentron\Facade\Mail\Entity;

use Kentron\Entity\Template\ACollectionEntity;

final class MailEmbedCollectionEntity extends ACollectionEntity
{
    public function __construct ()
    {
        parent::__construct(MailEmbedEntity::class);
    }
}
