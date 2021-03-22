<?php

namespace Kentron\Service\Http\Entity;

use Kentron\Entity\Entity;

use Kentron\Service\Http\Entity\TSoap;
use Kentron\Service\{Type, IGBinary, Xml};

final class HttpEntity extends Entity
{
    use TSoap;

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

    public const METHOD_GET = 1;
    public const METHOD_POST = 2;
    public const METHOD_PUT = 3;
    public const METHOD_DELETE = 4;
    public const METHOD_SOAP = 5;

    private $baseUrl;
    private $data;
    private $decodeAsArray = false;
    private $decoding = self::DECODE_JSON;
    private $encoding = self::ENCODE_JSON;
    private $extracted;
    private $getString = "";
    private $headers = [];
    private $httpMethod = self::METHOD_POST;
    private $postData;
    private $rawResponse;
    private $statusCode;
    private $success = false;
    private $uri;
    private $parameterise = false;

    /**
     * Getters
     */

    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    public function getExtracted()
    {
        return $this->extracted ?? null;
    }

    public function getUrl(): string
    {
        return rtrim("{$this->baseUrl}/{$this->uri}{$this->getString}", "/");
    }

    public function getPostData()
    {
        return $this->postData ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getData()
    {
        return $this->data ?? null;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Gets the CURL request method
     *
     * @return string
     *
     * @throws \UnexpectedValueException If the method is not allowed
     */
    public function getHttpMethod(): string
    {
        switch ($this->httpMethod) {
            case self::METHOD_GET:
                $httpMethod = "GET";
                break;

            case self::METHOD_POST:
                $httpMethod = "POST";
                break;

            case self::METHOD_PUT:
                $httpMethod = "PUT";
                break;

            case self::METHOD_DELETE;
                $httpMethod = "DELETE";
                break;

            default:
                throw new \UnexpectedValueException("CURL method given is unknown");
                break;
        }

        return $httpMethod;
    }

    /**
     * Setters
     */

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = rtrim($baseUrl, "/");
    }

    public function setDecodeToArray(bool $decodeAsArray = true): void
    {
        $this->decodeAsArray = $decodeAsArray;
    }

    public function setDecoding(int $decoding): void
    {
        $this->decoding = $decoding;
    }

    public function setEncoding(int $encoding): void
    {
        $this->encoding = $encoding;
    }

    /**
     * Sets the get data for the curl request
     *
     * @param mixed  $getData
     * @param string $usePrefix Put a ? at the start of the string
     *
     * @return void
     *
     * @throws \InvalidArgumentException If the get data is a resource
     */
    public function setGetData($getData, ?bool $usePrefix = true): void
    {
        if (is_resource($getData)) {
            throw new \InvalidArgumentException("Get data cannot be a resource");
        }

        if (is_array($getData) || is_object($getData)) {
            $getData = http_build_query($getData);
        }
        else {
            $this->getString = (string) $getData;
        }

        $getData = ltrim($getData, "?");
        $this->getString = $usePrefix ? "?" . $getData : $getData;
    }

    /**
     * Adds a header
     *
     * @param string $headerKey
     * @param string $headerValue
     *
     * @return void
     */
    public function addHeader(string $headerKey, string $headerValue): void
    {
        $this->headers[] = "$headerKey: $headerValue";
    }

    /**
     * Set using the constants
     *
     * @param integer $httpMethod Example: METHOD_GET
     *
     * @return void
     */
    public function setHttpMethod(int $httpMethod): void
    {
        $this->httpMethod = $httpMethod;
    }

    /**
     * Sets the post data for the curl request
     *
     * @param mixed $postData The post data
     *
     * @return void
     *
     * @throws \InvalidArgumentException If the post data is a resource
     * @throws \InvalidArgumentException If the post data is an invalid type for the encoding method
     * @throws \ErrorException           If the post data expects a file but it does not exist or is unreadable
     * @throws \UnexpectedValueException If the encoding method provided is not available
     */
    public function setPostData($postData): void
    {
        if (is_resource($postData)) {
            throw new \InvalidArgumentException("Post data cannot be a resource"); // TODO: Yet
        }

        switch ($this->encoding) {
            case self::ENCODE_NONE:
                if (!is_string($postData) && !(is_array($postData) && Type::isAssoc($postData))) {
                    throw new \InvalidArgumentException("Invalid post data type");
                }

                if (is_string($postData)) {
                    $this->setContentType("text/plain");
                    $this->setContentLength(strlen($postData));
                }
                else if (is_array($postData)) {
                    if ($this->parameterise) {
                        $postData = http_build_query($postData);
                        $this->setContentLength(strlen($postData));
                    }
                    else {
                        $this->setContentLength(strlen(json_encode($postData)));
                    }
                    $this->setContentType("application/x-www-form-urlencoded");
                }

                break;

            case self::ENCODE_JSON:
                $postData = json_encode($postData);
                $this->setContentType("application/json");
                $this->setContentLength(strlen($postData));

                break;

            case self::ENCODE_BINARY:
                $postData = IGBinary::serialise($postData);
                break;

            case self::ENCODE_FILE:
                $filePath = realpath($postData);
                if (empty($filePath) || !@file_exists($filePath) || !@is_readable($filePath)) {
                    throw new \ErrorException("File '$filePath' does not exist or is not readable");
                }

                $this->setContentType(mime_content_type($filePath));
                $postData = ["file" => "@$filePath"];

                break;

            case self::ENCODE_XML:
            case self::ENCODE_SOAP:
                if (isset($this->viewPath) && isset($this->method)) {
                    $postData = Xml::build($this->viewPath, $this->method, $postData);
                }
                if (is_null($postData) || is_null(Xml::extract($postData))) {
                    throw new \ErrorException("Post data is not valid XML");
                }

                $this->setContentLength(strlen($postData));
                $this->setContentType("application/soap+xml");

                break;

            default:
                throw new \UnexpectedValueException("Encoding method given is unknown");
                break;
        }

        $this->postData = $postData;
    }

    /**
     * Set the HTTP status code (200, 404, 501, ...)
     *
     * @param integer $statusCode
     *
     * @return void
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Set the URI succeeding the domain
     *
     * @param string $uri
     *
     * @return void
     */
    public function setUri(string $uri): void
    {
        $this->uri = trim($uri, "/");
    }

    /**
     * Set whether the post data should be parameterised
     *
     * @param boolean $parameterise
     *
     * @return void
     */
    public function setParameterisePostData(bool $parameterise = true): void
    {
        $this->parameterise = $parameterise;
    }

    /**
     * Set a bearer authorisation token
     *
     * @param string $token
     *
     * @return void
     */
    public function setBearerToken(string $token): void
    {
        $this->setAuthorisation("Bearer $token");
    }

    public function setContentLength(int $contentLength): void
    {
        $this->addHeader("Content-Length", $contentLength);
    }

    /**
     * Helpers
     */

    public function isPost(): bool
    {
        return !is_null($this->postData);
    }

    public function isCurl(): bool
    {
        return $this->httpMethod !== self::METHOD_SOAP;
    }

    /**
     * Decode the response from CURL
     *
     * @param mixed $response
     *
     * @return void
     *
     * @throws \UnexpectedValueException If the decoding method provided is not available
     */
    public function parseResponse($response): void
    {
        if (is_string($response)) {
            $this->rawResponse = $response;
        }
        else {
            $this->rawResponse = json_encode($response);
        }

        switch ($this->decoding) {
            case self::DECODE_NONE:
                $extracted = $response;
                break;

            case self::DECODE_JSON:
                $extracted = json_decode($response, $this->decodeAsArray);

                if (is_null($extracted)) {
                    $this->addError("Response from '{$this->getUrl()}' could not be JSON decoded");
                    return;
                }
                // Overwrite the raw response to a minimal version of the JSON
                $this->rawResponse = json_encode($extracted);

                break;

            case self::DECODE_BINARY:
                $extracted = IGBinary::unserialise($response);

                if (is_null($extracted)) {
                    $this->addError("Response from '{$this->getUrl()}' could not be binary decoded");
                    return;
                }
                break;

            case self::DECODE_XML:
                $extracted = Xml::extract($response);

                if (is_null($extracted)) {
                    $this->addError("Response from '{$this->getUrl()}' could not be XML decoded");
                    return;
                }
                break;

            case self::DECODE_SOAP:
                $extracted = Xml::extract(
                    preg_replace(
                        ['/(<\/?)(\w+):([^>]*>)/', '/&lt;/', '/&gt;/'],
                        ['$1$2$3', '<', '>'],
                        $response
                    )
                );

                if (is_null($extracted)) {
                    $this->addError("Response from '{$this->getUrl()}::{$this->getMethod()}' could not be SOAP decoded");
                    return;
                }

                $extracted = $extracted["soapBody"] ?? $extracted;
                break;

            default:
                throw new \UnexpectedValueException("Decoding method given is unknown");
                break;
        }

        if (is_array($extracted) || is_object($extracted)) {
            $this->setExtractedData($extracted);
        }

        $this->extracted = $extracted;
    }

    /**
     * Private functions
     */

    /**
     * Set local specific response post data
     *
     * @param array|object $extracted
     *
     * @return void
     */
    private function setExtractedData($extracted): void
    {
        $this->setSuccess(Type::getProperty($extracted, "success") ?? true);
        $this->setData(Type::getProperty($extracted, "data") ?? null);
        $this->addError(Type::getProperty($extracted, "errors") ?? []);
    }

    private function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    private function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * Set an authorisation header
     *
     * @param string $authorisation
     *
     * @return void
     */
    private function setAuthorisation(string $authorisation): void
    {
        $this->addHeader("Authorization", $authorisation);
    }

    /**
     * Set the request content type
     *
     * @param string $contentType
     *
     * @return void
     */
    private function setContentType(string $contentType): void
    {
        $this->addHeader("Content-Type", $contentType);
    }
}
