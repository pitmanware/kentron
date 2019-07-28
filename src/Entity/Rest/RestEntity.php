<?php

    namespace Kentron\Entity\Rest;

    use Kentron\Entity\Entity;

    /**
     * An Entity for standardising the data needed for the Rest service
     */
    final class RestEntity extends Entity
    {
        private $baseUrl;
        private $decodeAsArray = false;
        private $extractedArray;
        private $extractedObject;
        private $headers;
        private $getString;
        private $method;
        private $postDataString;
        private $postDataArray;
        private $success = true;
        private $rawResponse;
        private $sendPostDataAsJson = false;

        /**
         * Getters
         */

        public function getApiKey (): string
        {
            return $this->apiKey;
        }

        public function getData ()
        {
            return $this->extractedArray["data"] ?? $this->extractedObject->data ?? null;
        }

        public function getExtracted ()
        {
            return $this->extractedArray ?? $this->extractedObject ?? null;
        }

        public function getHeaders (): array
        {
            return $this->headers ?? [];
        }

        public function getPostData ()
        {
            return $this->postDataString ?? $this->postDataArray;
        }

        public function getRawResponse (): ?string
        {
            return $this->rawResponse;
        }

        public function getSuccess (): bool
        {
            return $this->success;
        }

        public function getUrl (): string
        {
            return rtrim("$this->baseUrl/$this->method/$this->getString", "/");
        }

        /**
         * Setters
         */

        public function setHeaders (array $headers): void
        {
            $this->headers = $headers;
        }

        public function setBaseUrl (string $baseUrl): void
        {
            $this->baseUrl = rtrim($baseUrl, "/");
        }

        public function setDecodeToArray (bool $decodeAsArray): void
        {
            $this->decodeAsArray = $decodeAsArray;
        }

        public function setMethod (string $method): void
        {
            $this->method = trim($method, "/");
        }

        public function setGetString (string $getString): void
        {
            $this->getString = trim($getString, "/");
        }

        public function setPostData (array $postData): void
        {
            if ($this->sendPostDataAsJson) {
                $this->setPostDataString(json_encode($postData));
            }
            else {
                $this->setPostDataArray($postData);
            }
        }

        public function setSendPostDataAsJson (bool $sendPostDataAsJson): void
        {
            $this->sendPostDataAsJson = $sendPostDataAsJson;
        }

        /**
         * Helpers
         */

        public function addHeader (string $header, string $value): void
        {
            $this->headers[] = "$header: $value";
        }

        public function isPost (): bool
        {
            return !is_null($this->postDataString) || !is_null($this->postDataArray);
        }

        public function parseResponse (string $response): void
        {
            $this->rawResponse = $response;

            $extracted = json_decode($response, $this->decodeAsArray);

            if (is_null($extracted)) {
                $this->addError("JSON data could not be decoded");
                return;
            }

            if ($this->decodeAsArray) {
                $this->setExtractedArray($extracted);
            }
            else {
                $this->setExtractedObject($extracted);
            }
        }

        /**
         * Private functions
         */

         private function setPostDataArray (array $postData): void
         {
             $this->postDataArray = $postData;
         }

         private function setPostDataString (string $postData): void
         {
             $this->postDataString = $postData;
         }

         private function setExtractedArray (array $extracted): void
         {
             $this->extractedArray = $extracted;

             $this->setSuccess($extracted["success"] ?? true);

             $this->addError($extracted["errors"] ?? []);
         }

         private function setExtractedObject (object $extracted): void
         {
             $this->extractedObject = $extracted;

             $this->setSuccess($extracted->success ?? true);

             $this->addError($extracted->errors ?? []);
         }

         private function setSuccess (bool $success): void
         {
             $this->success = $success;
         }
    }
