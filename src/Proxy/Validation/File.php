<?php

    namespace Kentron\Proxy\Validation;

    final class File
    {
        private $filePath;
        private $fileResource;

        public function __construct (?string $filePath = null)
        {
            if (is_string($filePath)) {
                $this->setFilePath($filePath);
            }
        }

        public function setFilePath (string $filePath): void
        {
            $this->filePath = $filePath;
        }

        public function exists (): bool
        {
            return file_exists($this->filePath);
        }

        public function empty (): bool
        {
            return filesize($this->filePath) === 0;
        }

        public function readable (): bool
        {
            return is_readable($this->filePath);
        }

        public function writeable (): bool
        {
            return is_writeable($this->filePath);
        }

        public function getContent (): ?string
        {
            if (!$this->exists() || !$this->readable()) {
                return null;
            }

            return file_get_contents($this->filePath);
        }

        public function stream (): void
        {
            if ($this->open()) {
                // Dunno yet
            }
        }

        private function open (): bool
        {
            if (!$this->exists() || $this->readable()) {
                return false;
            }

            $this->fileResource = fopen($this->filePath);
            return true;
        }
    }
