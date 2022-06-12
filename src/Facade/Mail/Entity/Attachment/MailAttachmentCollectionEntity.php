<?php
declare(strict_types=1);

namespace Kentron\Facade\Mail\Entity\Attachment;

use Kentron\Entity\Template\ACollectionEntity;

final class MailAttachmentCollectionEntity extends ACollectionEntity
{
    public function __construct()
    {
        parent::__construct(MailAttachmentEntity::class);
    }
}
