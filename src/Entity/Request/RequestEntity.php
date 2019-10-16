<?php

    namespace Kentron\Request\Entity;

    use Kentron\Entity\Entity;
    use Kentron\Facade\Xml as XmlFacade;

    use \SimpleXMLElement;

    use Kentron\Proxy\{Type,IGBinary};

    final class RequestEntity extends Entity
    {
        public const ENCODE_NONE = 1;
        public const ENCODE_JSON = 2;
        public const ENCODE_BINARY = 3;
        public const ENCODE_FILE = 4;
        public const ENCODE_XML = 5;
        public const ENCODE_SOAP = 6;

        public const DECODE_NONE = 1;
        public const DECODE_JSON = 2;
        public const DECODE_BINARY = 3;
        public const DECODE_XML = 4;
        public const DECODE_SOAP = 5;

        private $baseUrl;
        private $data;
        private $decodeAsArray = false;
        private $decoding = self::DECODE_JSON;
        private $encoding = self::ENCODE_JSON;
        private $extracted;
        private $getString = "";
        private $headers = [];
        private $postData;
        private $rawResponse;
        private $success = false;
        private $uri;

        /**
         * Getters
         */

        public function getRawResponse (): ?string
        {
            return $this->rawResponse;
        }

        public function getExtracted ()
        {
            return $this->extracted ?? null;
        }

        public function getUrl (): string
        {
            return "{$this->baseUrl}/{$this->uri}{$this->getString}";
        }

        public function getPostData ()
        {
            return $this->postData ?? null;
        }

        public function getHeaders (): array
        {
            return $this->headers;
        }

        public function getData ()
        {
            return $this->data ?? null;
        }

        public function getSuccess (): bool
        {
            return $this->success;
        }

        /**
         * Setters
         */

        public function setBaseUrl (string $baseUrl): void
        {
            $this->baseUrl = rtrim($baseUrl, "/");
        }

        public function setDecodeToArray (bool $decodeAsArray = true): void
        {
            $this->decodeAsArray = $decodeAsArray;
        }

        public function setDecoding (int $decoding): void
        {
            $this->decoding = $decoding;
        }

        public function setEncoding (int $encoding): void
        {
            $this->encoding = $encoding;
        }

        /**
         * Sets the get data for the curl request
         * @param mixed  $getData
         * @param string $usePrefix Put a ? at the start of the string
         *
         * @throws \InvalidArgumentException If the get data is a resource
         */
        public function setGetData ($getData, ?bool $usePrefix = true): void
        {
            if (is_resource($getData))
            {
                throw new \InvalidArgumentException("CURL get data cannot be a resource");
            }

            if (is_array($getData) || is_object($getData))
            {
                $getData = http_build_query($getData);
            }
            else
            {
                $this->getString = (string) $getData;
            }

            $getData = ltrim($getData, "?");
            $this->getString = $usePrefix ? "?" . $getData : $getData;
        }

        public function addHeader (string $headerKey, string $headerValue): void
        {
            $this->headers[] = "$headerKey: $headerValue";
        }

        /**
         * Sets the post data for the curl request
         * @param  mixed $postData The post data
         *
         * @throws \InvalidArgumentException If the post data is a resource
         * @throws \InvalidArgumentException If the post data is an invalid type for the encoding method
         * @throws \ErrorException           If the post data expects a file but it does not exist or is unreadable
         * @throws \UnexpectedValueException If the encoding method provided is not available
         */
        public function setPostData ($postData): void
        {
            if (is_resource($postData)) {
                throw new \InvalidArgumentException("CURL post data cannot be a resource");
            }

            switch ($this->encoding) {
                case self::ENCODE_NONE:
                    if (!is_string($postData) && !(is_array($postData) && Type::isAssoc($postData)))
                    {
                        throw new \InvalidArgumentException("Invalid post data type");
                    }
                    break;

                case self::ENCODE_JSON:
                    $postData = json_encode($postData);
                    break;

                case self::ENCODE_BINARY:
                    $postData = IGBinary::serialise($postData);
                    break;

                case self::ENCODE_FILE:
                    $filePath = realpath($postData);
                    if (empty($filePath) || !@file_exists($filePath) || !@is_readable($filePath))
                    {
                        throw new \ErrorException("File '$filePath' does not exist or is not readable");
                    }

                    $postData = ["file" => "@$filePath"];
                    break;

                case self::ENCODE_XML:
                case self::ENCODE_SOAP:
                    try
                    {
                        XmlFacade::extract($postData);
                    }
                    catch (\Exception $ex)
                    {
                        throw new \ErrorException("CURL post data is not valid XML", null, $ex);
                    }
                    break;

                default:
                    throw new \UnexpectedValueException("Encoding method given is unknown");
                    break;
            }

            $this->postData = $postData;
        }

        public function setUri (string $uri): void
        {
            $this->uri = trim($uri, "/");
        }

        /**
         * Helpers
         */

        public function isPost (): bool
        {
            return !is_null($this->postData);
        }

        /**
         * Decode the response from CURL
         * @param string $response [description]
         *
         * @throws \UnexpectedValueException If the decoding method provided is not available
         */
        public function parseResponse (string $response): void
        {
            $this->rawResponse = $response;

            switch ($this->decoding) {
                case self::DECODE_NONE:
                    $extracted = $response;
                    break;

                case self::DECODE_JSON:
                    $extracted = json_decode($response, $this->decodeAsArray);

                    if (is_null($extracted))
                    {
                        $this->addError("Response from '{$this->getUrl()}' could not be JSON decoded");
                        return;
                    }
                    break;

                case self::DECODE_BINARY:
                    $extracted = IGBinary::unserialise($response);

                    if (is_null($extracted))
                    {
                        $this->addError("Response from '{$this->getUrl()}' could not be binary decoded");
                        return;
                    }
                    break;

                case self::DECODE_XML:
                    try
                    {
                        $extracted = XmlFacade::extract($response);
                    }
                    catch (\Exception $ex)
                    {
                        $this->addError("Response from '{$this->getUrl()}' could not be XML decoded");
                        return;
                    }
                    break;

                case self::DECODE_SOAP:
                    try
                    {
                        $extracted = XmlFacade::extractSoap($response);
                    }
                    catch (\Extracted $ex)
                    {
                        $this->addError("Response from '{$this->getUrl()}' could not be SOAP decoded");
                        return;
                    }

                    $extracted = $extracted["soapBody"] ?? $extracted;
                    break;

                default:
                    throw new \UnexpectedValueException("Decoding method given is unknown");
                    break;
            }

            if (is_array($extracted)) {
                $this->setExtractedArrayData($extracted);
            }
            else if (is_object($extracted)) {
                $this->setExtractedObjectData($extracted);
            }

            $this->extracted = $extracted;
        }

        /**
         * Private functions
         */

        private function setExtractedArrayData (array $extracted): void
        {
            $this->setSuccess($extracted["success"] ?? true);
            $this->setData($extracted["data"] ?? null);
            $this->addError($extracted["errors"] ?? []);
        }

        private function setExtractedObjectData (object $extracted): void
        {
            $this->setSuccess($extracted->success ?? true);
            $this->setData($extracted->data ?? null);
            $this->addError($extracted->errors ?? []);
        }

        private function setSuccess (bool $success): void
        {
            $this->success = $success;
        }

        private function setData ($data): void
        {
            $this->data = $data;
        }
    }
