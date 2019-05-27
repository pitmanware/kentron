<?php

    namespace Kentron\Proxy;

    use Kentron\Template\IError;

    class Response implements IError
    {
        public $body = [
            "success"   => false,
            "data"      => null,
            "errors"    => []
        ];

        private $statusCode = 200;

        /**
         *
         * Getters
         *
         */

        final public function getBody (): array
        {
            return $this->body;
        }

        final public function getErrors (): array
        {
            return $this->body["errors"];
        }

        /**
         *
         * Setters
         *
         */

        final public function setErrors (array $errors): void
        {
            $this->body["errors"] = $errors;
        }

        final public function setSuccess (bool $success = true): void
        {
            $this->body["success"] = $success;
        }

        final public function setData (array $data): void
        {
            $this->body["data"] = $data;
        }

        final public function setStatusCode (int $statusCode): void
        {
            $this->statusCode = $statusCode;
        }

        final public function setLocation (string $url): void
        {
            $this->setHeader("Location", $url);
        }

        final public function setContentType (string $contentType): void
        {
            $this->setHeader("Content-Type", $contentType);
        }

        /**
         *
         * Helper functions
         *
         */

        final public function addError ($errors): void
        {
            if (is_array($errors)) {
                $errors = array_merge($this->getErrors(), $errors);
                $this->setErrors($errors);
            }
            else {
                $this->body["errors"][] = $errors;
            }
        }

        final public function hasErrors (): bool
        {
            return count($this->getErrors()) > 0;
        }

        final public function dispatch (): void
        {
            $this->setContentType("application/json");

            if (!$this->hasErrors()) {
                $this->setSuccess(true);
            }

            echo json_encode($this->getBody());
            die;
        }

        final public function redirect (string $url): void
        {
            $this->setLocation($url);
            die;
        }

        /**
         *
         * Wrapper functions
         *
         */

        final public function setResponseCode (): void
        {
            http_response_code($this->statusCode);
        }

        final public function setHeader (string $header, string $value): void
        {
            header("$header: $value");
        }

    }
