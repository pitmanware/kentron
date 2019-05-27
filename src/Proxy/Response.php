<?php

    namespace Kentron\Proxy;

    use Kentron\Template\AError;

    final class Response extends AError
    {
        /**
         * The success of the response, used primarilty with api/ajax routes
         * @var bool
         */
        private $success = false;

        /**
         * The response data, JSON encoded on dispatch
         * @var mixed
         */
        private $data;

        /**
         * The response code, defaults to 200
         * @var int
         */
        private $statusCode = 200;

        /**
         * Getters
         */

        /**
         * Get the body of the response data
         * @return array
         */
        public function getBody (): array
        {
            return [
                "success" => $this->success,
                "data"    => $this->data,
                "errors"  => $this->getErrors()
            ];
        }

        public function getData       ()       { return $this->data;       }
        public function getStatusCode (): int  { return $this->statusCode; }
        public function getSuccess    (): bool { return $this->success;    }

        /**
         * Setters
         */

        public function setSuccess (bool $success = true): void
        {
            $this->success = $success;
        }

        /**
         * Set the response data
         * @param mixed $data This will get JSON encoded so type doesn't matter
         */
        public function setData ($data): void
        {
            $this->data = $data;
        }

        /**
         * Set the status code of the response
         * @param int $statusCode
         */
        public function setStatusCode (int $statusCode): void
        {
            $this->statusCode = $statusCode;
        }

        /**
         * Set the location header
         * @param string $url
         */
        public function setLocation (string $url): void
        {
            $this->setHeader("Location", $url);
        }

        /**
         * Set the content type header
         * @param string $contentType
         */
        public function setContentType (string $contentType): void
        {
            $this->setHeader("Content-Type", $contentType);
        }

        /**
         * Helper methods
         */

        /**
         * Dispatch the response and kill the script
         * @return void
         */
        public function dispatch (): void
        {
            $this->setResponseCode();
            $this->setContentType("application/json");

            if (!$this->hasErrors()) {
                $this->setSuccess();
            }

            echo json_encode($this->getBody());
            die;
        }

        /**
         * Set the location header and kill the script
         * @param string $url
         */
        public function redirect (string $url): void
        {
            $this->setResponseCode();
            $this->setLocation($url);
            die;
        }

        /**
         * Wrapper methods
         */

        /**
         * Set the http response code
         * @return void
         */
        private function setResponseCode (): void
        {
            http_response_code($this->statusCode);
        }

        /**
         * Dynamically set any http header
         * @param string $header
         * @param string $value
         */
        private function setHeader (string $header, string $value): void
        {
            header("$header: $value");
        }
    }
