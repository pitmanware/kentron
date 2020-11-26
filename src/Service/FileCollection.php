<?php

namespace Kentron\Service;

use Kentron\Entity\File\{FileCollectionEntity, FileEntity};

use Kentron\Exception\FilesFormatException;

use Kentron\Service\System\Files;

final class FileCollection
{
    /**
     * Build the array of File Entities
     */
    final public function build (): FileCollectionEntity
    {
        $fileColletionEntity = new FileCollectionEntity();
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
                $files["name"][$key],
                $files["type"][$key],
                $files["tmp_name"][$key],
                $files["size"][$key]
            );

            $fileColletionEntity->addEntity($fileEntity);
        }

        return $fileColletionEntity;
    }
}
