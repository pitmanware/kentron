<?php

    namespace Kentron\Entity\File;

    use Kentron\Entity\Entity;

    class FileEntity extends Entity
    {
        private $name       = "";
        private $extension  = "";
        private $type       = "";
        private $path       = "";
        private $size       = null;

        public function __construct (string $name, string $type, string $path, string $size)
        {
            $this->name       = $name;
            $this->type       = $type;
            $this->path       = $path;
            $this->size       = $size;
            $this->extension  = pathinfo($name, PATHINFO_EXTENSION) ?? "";
        }

        public function rename (string $newName): void
        {
            $newName = preg_replace("/[^\d\w]/", '', $newName);

            rename($this->path, "{$this->getDirectory()}/$newName");
        }

        public function delete (): void
        {
            unlink($this->path);
        }

        public function move (string $targetDirectory): void
        {
            $targetDirectory = rtrim($targetDirectory, '/');

            if (!is_dir($targetDirectory)) {
                $this->addError("Directory '$targetDirectory' does not exist");
            }

            move_uploaded_file($this->path, "$targetDirectory/$this->name");
        }

        /**
         *
         * Getters
         *
         */

        public function getContents (): string
        {
            return file_get_contents($this->path);
        }

        public function getName ()
        {
            return $this->name;
        }

        public function getType ()
        {
            return $this->type;
        }

        public function getExtension ()
        {
            return $this->extension;
        }

        public function getPath ()
        {
            return $this->path;
        }

        public function getSize ()
        {
            return $this->path;
        }

        public function getDirectory ()
        {
            return dirname($this->getPath());
        }
    }
