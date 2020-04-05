<?php

namespace Kentron\Entity\File;

use Kentron\Template\Entity\ACollectionEntity;

final class FileCollectionEntity extends ACollectionEntity
{
    public function __construct ()
    {
        parent::__construct(FileEntity::class);
    }
}
