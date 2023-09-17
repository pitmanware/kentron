<?php
declare(strict_types=1);

namespace Kentron\Facade\Mail\Entity;

use Kentron\Template\Entity\ACollectionEntity;

final class MailTargetCollectionEntity extends ACollectionEntity
{
    public function __construct()
    {
        parent::__construct(MailTargetEntity::class);
    }
}
