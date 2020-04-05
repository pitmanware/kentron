<?php

namespace Kentron\Entity\File;

use Kentron\Template\Entity\AEntity;
use Kentron\Facade\File;

final class FileEntity extends AEntity
{
    private $file;

    public function getFile (): File
    {
        return $this->file;
    }

    public function setFile (File $file): void
    {
        $this->file = $file;
    }
}
