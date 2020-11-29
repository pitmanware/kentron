<?php

namespace Kentron\Facade\Mail\Entity;

use Kentron\Entity\Template\ACollectionEntity;

final class MailAttachmentCollectionEntity extends ACollectionEntity
{
    public function __construct ()
    {
        parent::__construct(MailAttachmentEntity::class);
    }
}
