<?php

namespace Kentron\Entity\File;

use Kentron\Entity\Template\ACollectionEntity;

final class FileCollectionEntity extends ACollectionEntity
{
    public function __construct ()
    {
        parent::__construct(FileEntity::class);
    }
}
