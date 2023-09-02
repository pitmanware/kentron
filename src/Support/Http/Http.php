<?php
declare(strict_types=1);

namespace Kentron\Support\Http;

use Kentron\Enum\EHttpMethod;
use Kentron\Enum\EType;
use Kentron\Support\Http\TSoap;
use Kentron\Support\Json;
use Kentron\Support\Type\Type;
use Kentron\Support\Xml;
use Kentron\Support\Curl;
use Kentron\Template\Alert\TError;

use \SoapClient;
use \Throwable;

final class Http
{
    use TSoap;
    use TError;

    public bool $decodeAsArray = false;
    public bool $urlEncode = false;
    public EDecodeAs $decoding = EDecodeAs::Json;
    public EEncodeAs $encoding = EEncodeAs::Json;
    public EHttpMethod $httpMethod = EHttpMethod::Post;
    public string $baseUrl;
    public string $getString = "";
    public string $uri = "";
    public array $headers = [];
    public mixed $postData = null;

    private bool $success = true;
    private mixed $data;
    private int|null $statusCode = null;
    private string|null $rawResponse = null;
    private object|array|null $extracted = null;

    /**
     * Getters
     */

    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    public function getExtracted(): object|array|null
    {
        return $this->extracted ?? null;
    }

    public function getUrl(): string
    {
        return rtrim(rtrim($this->baseUrl, "/") . "/{$this->uri}{$this->getString}", "/");
    }

    public function getFormattedHeaders(): array
    {
        $headers = [];

        foreach ($this->headers as $headerKey => $headerValue) {
            $headers[] = "{$headerKey}: " . Type::castToString($headerValue);
        }

        return $headers;
    }

    public function getData(): mixed
    {
        return $this->data ?? null;
    }

    public function getStatusCode(): ?int
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
     * @throws \UnexpectedValueException If the method is unknown
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod->value;
    }

    /**
     * Returns a stringified version of the post data for auditing
     *
     * @return string|null
     */
    final public function getPostDataAsString(): ?string
    {
        if (is_string($this->postData) || is_null($this->postData)) {
            return $this->postData;
        }
        else if (is_array($this->postData) || is_object($this->postData)) {
            return Json::toString($this->postData);
        }

        return Type::cast($this->postData)->quietly()->toString();
    }

    /**
     * Setters
     */

    /**
     * Sets the get data for the curl request
     *
     * @param mixed $getData
     * @param string $usePrefix Put a ? at the start of the string
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
            $getData = (string) $getData;
        }

        $this->getString = $usePrefix ? "?" . ltrim($getData, "?") : $getData;
    }

    public function addHeader(string $headerKey, string|int $headerValue, bool $override = false): void
    {
        if (!isset($this->headers[$headerKey]) || $override) {
            $this->headers[$headerKey] = $headerValue;
        }
    }

    /**
     * Sets the post data for the curl request
     * @param mixed $postData The post data
     *
     * @throws \InvalidArgumentException If the post data is a resource
     * @throws \InvalidArgumentException If the post data is an invalid type for the encoding method
     * @throws \ErrorException           If the post data expects a file but it does not exist or is unreadable
     * @throws \UnexpectedValueException If the encoding method provided is not available
     */
    public function setPostData($postData): void
    {
        if (is_resource($postData)) {
            throw new \InvalidArgumentException("Post data cannot be a resource"); // TODO: Yet (7.4)
        }

        switch ($this->encoding) {
            case EEncodeAs::None:
                if (!is_string($postData) && !(is_array($postData) && Type::isAssoc($postData))) {
                    throw new \InvalidArgumentException("Invalid post data type");
                }

                if (is_string($postData)) {
                    $this->setContentType("text/plain");
                    $this->setContentLength(strlen($postData));
                }
                else if (is_array($postData)) {
                    if ($this->urlEncode) {
                        $postData = http_build_query(data: $postData, encoding_type: PHP_QUERY_RFC3986);
                        $this->setContentType("application/x-www-form-urlencoded");
                    }
                    else {
                        $postData = http_build_query(data: $postData, encoding_type: PHP_QUERY_RFC1738);
                        $this->setContentType("multipart/form-data");
                    }

                    $this->setContentLength(strlen($postData));
                }

                break;

            case EEncodeAs::Json:
                $postData = json_encode($postData);
                if ($postData === false) {
                    throw new \ErrorException("Post data could not be JSON encoded: " . Json::handleError());
                }

                $this->setContentType("application/json");
                $this->setContentLength(strlen($postData));

                break;

            case EEncodeAs::Serial:
                $postData = serialize($postData);
                break;

            case EEncodeAs::File:
                $filePath = realpath($postData);
                if (empty($filePath) || !@file_exists($filePath) || !@is_readable($filePath)) {
                    throw new \ErrorException("File '$filePath' does not exist or is not readable");
                }

                $this->setContentType(mime_content_type($filePath));
                $postData = ["file" => "@$filePath"];

                break;

            case EEncodeAs::Xml:
            case EEncodeAs::Soap:
                if (isset($this->viewPath) && isset($this->method)) {
                    $postData = Xml::build($this->viewPath, $this->method, Type::cast($postData)->toArray());
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

    public function setUri(string $uri): void
    {
        $this->uri = trim($uri, "/");
    }

    public function setUrlEncode(bool $urlEncode = true): void
    {
        $this->urlEncode = $urlEncode;
    }

    public function setBearerToken(string $token): void
    {
        $this->setAuthorisation("Bearer $token");
    }

    /**
     * Headers
     */

    public function setContentType(string $contentType): void
    {
        $this->addHeader("Content-Type", $contentType);
    }

    public function setContentLength(int $contentLength): void
    {
        $this->addHeader("Content-Length", $contentLength);
    }

    public function setAuthorisation(string $authorisation): void
    {
        $this->addHeader("Authorization", $authorisation);
    }

    /**
     * Helpers
     */

    /**
     * Make the HTTP request
     *
     * @return bool The success of the request
     */
    public function run(): bool
    {
        if ($this->httpMethod === EHttpMethod::Soap) {
            self::runSoap();
        }
        else {
            self::runCurl();
        }

        return $this->success;
    }

    public function isPost(): bool
    {
        return !is_null($this->postData);
    }

    /**
     * Private functions
     */

    /**
     * Make the curl request using the info provided by the entity
     *
     * @return void
     */
    private function runCurl(): void
    {
        $curl = new Curl();

        $curl->setUrl($this->getUrl());
        $curl->setHeaders($this->getFormattedHeaders());

        if ($this->isPost()) {
            $curl->setPost($this->postData);
        }

        $curl->setRequestMethod($this->getHttpMethod());

        if (!$curl->execute()) {
            $this->addError($curl->errors);
        }
        else {
            $this->statusCode = $curl->getStatusCode();
            $this->parseResponse($curl->getResponse());
        }

        if ($this->hasErrors()) {
            $this->success = false;
        }
    }

    /**
     * Make the SOAP request using the info provided by the entity
     *
     * @return void
     */
    private function runSoap(): void
    {
        try {
            $soap = new SoapClient(
                $this->getWsdlUrl(),
                $this->getConfig()
            );

            $method = $this->getMethod();

            if (is_array($this->postData)) {
                $soap->__setSoapHeaders($this->getFormattedHeaders());

                $response = $soap->$method($this->postData);
            }
            else if (is_string($this->postData)) {
                $response = $soap->__doRequest(
                    $this->postData,
                    $this->getWsdlUrl(),
                    $method,
                    $this->getSoapVersion(),
                    false
                );
            }

            $this->setRawRequest($soap->__getLastRequest());
            $this->parseResponse(Type::getProperty($response, "{$method}Result") ?? $response);
        }
        catch (Throwable $th) {
            $this->addError($th->getMessage());
        }

        if ($this->hasErrors()) {
            $this->success = false;
        }
    }

    /**
     * Decode the response from CURL
     *
     * @param mixed $response
     *
     * @throws \UnexpectedValueException If the decoding method provided is not available
     */
    public function parseResponse(mixed $response): void
    {
        if (is_string($response)) {
            $this->rawResponse = $response;
        }
        else {
            $this->rawResponse = json_encode($response);
        }

        switch ($this->decoding) {
            case EDecodeAs::None:
                $extracted = $response;
                break;

            case EDecodeAs::Json:
                $extracted = json_decode($response, $this->decodeAsArray);

                if (is_null($extracted)) {
                    $this->addError("Response from '{$this->getUrl()}' could not be JSON decoded");
                    return;
                }
                // Overwrite the raw response to a minimal version of the JSON
                $this->rawResponse = json_encode($extracted);

                break;

            case EDecodeAs::Serial:
                $extracted = unserialize($response);

                if (is_null($extracted)) {
                    $this->addError("Response from '{$this->getUrl()}' could not be decoded");
                    return;
                }
                break;

            case EDecodeAs::Xml:
                $extracted = Xml::extract($response);

                if (is_null($extracted)) {
                    $this->addError("Response from '{$this->getUrl()}' could not be XML decoded");
                    return;
                }
                break;

            case EDecodeAs::Soap:
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

        $this->success = Type::getProperty($extracted, "success") ?? true;
        $this->data = Type::getProperty($extracted, "data") ?? null;
        $this->extracted = $extracted;

        $errors = Type::getProperty($extracted, "errors") ?? [];
        if (Type::of($errors)->isArrayOf(EType::String)) {
            $this->addError($errors);
        }
    }
}
