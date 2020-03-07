<?php

namespace Kentron\Service;

use Kentron\Entity\File\{FileCollectionEntity, FileEntity};

use Kentron\Exception\FilesFormatException;

use Kentron\Service\System\Files;

class FileCollection
{
    private $fileColletionEntity = null;

    public function __construct ()
    {
        $this->fileColletionEntity = new FileCollectionEntity();
    }

    /**
     * Build the array of File Entities
     */
    final public function build (): FileCollectionEntity
    {
        $files = Files::getAll();

        if (empty($files)) {
            throw new FilesFormatException("'\$_FILES' array is empty");
        }

        if (!isset($files["name"])) {
            throw new FilesFormatException("'\$_FILES' array is not one-dimensional");
        }

        $fileCount = count($files["name"]);

        for ($key = 0; $key < $fileCount; $key++) {

            $fileEntity = new FileEntity(
                $file["name"][$key],
                $file["type"][$key],
                $file["tmp_name"][$key],
                $file["size"][$key]
            );

            $this->fileColletionEntity->addFileEntity($fileEntity);
        }

        return $this->fileColletionEntity;
    }
}
