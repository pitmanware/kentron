<?php

    namespace Kentron\Entity\File;

    use Kentron\Entity\Entity;

    class FileCollectionEntity extends Entity
    {

        protected $collection = [];

        public function addFileEntity (FileEntity $fileEntity)
        {
            $this->collection[] = $fileEntity;
        }

        public function getCollection (): array
        {
            return $this->collection;
        }

    }
