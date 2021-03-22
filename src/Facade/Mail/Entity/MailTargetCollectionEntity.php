<?php

namespace Kentron\Facade\Mail\Entity;

use Kentron\Entity\Template\ACollectionEntity;

final class MailTargetCollectionEntity extends ACollectionEntity
{
    public function __construct()
    {
        parent::__construct(MailTargetEntity::class);
    }
}
