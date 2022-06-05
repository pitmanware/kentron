<?php
declare(strict_types=1);

namespace Kentron\Facade\Mail\Entity\Embed;

use Kentron\Entity\Template\ACollectionEntity;

final class MailEmbedCollectionEntity extends ACollectionEntity
{
    public function __construct()
    {
        parent::__construct(MailEmbedEntity::class);
    }
}
