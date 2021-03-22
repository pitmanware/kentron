<?php

namespace Kentron\Service\Http\Entity;

use \SoapVar;
use \SoapHeader;

trait TSoap
{
    private $config;
    private $textEncoding = "UTF-8";
    private $exceptions = true;
    private $extracted;
    private $features = SOAP_SINGLE_ELEMENT_ARRAYS | SOAP_USE_XSI_ARRAY_TYPE;
    private $method;
    private $parameters;
    private $password;
    private $rawRequest;
    private $soapVersion = SOAP_1_2;
    private $trace = true;
    private $username;
    private $viewPath;
    private $wsseUrl = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";

    /**
     * Getters
     */

    public function getConfig(): array
    {
        return [
            "trace"      => $this->trace,
            "exceptions" => $this->exceptions,
            "login"      => $this->username,
            "password"   => $this->password,
            "encoding"   => $this->textEncoding,
            "features"   => $this->features
        ];
    }

    public function getExtracted(): ?object
    {
        return $this->extracted;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getSoapVersion(): int
    {
        return $this->soapVersion;
    }

    public function getWsdlUrl(): string
    {
        return $this->getUrl();
    }

    /**
     * Setters
     */

    public function setTextEncoding(string $textEncoding): void
    {
        $this->textEncoding = $textEncoding;
    }

    public function setExceptions(bool $exceptions): void
    {
        $this->exceptions = $exceptions;
    }

    public function setFeatures(int $features): void
    {
        $this->features = $features;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRawRequest(string $rawRequest): void
    {
        $this->rawRequest = $rawRequest;
    }

    /**
     * Sets the SOAP WSSE header
     *
     * @return void
     */
    public function setSecurityHeader(): void
    {
        $this->headers[] = new SoapHeader(
            $this->wsseUrl,
            "Security",
            $this->newSoapVar(
                $this->newSoapVar(
                    [
                        "UsernameToken" => $this->newSoapVar(
                            [
                                "Username" => $this->newSoapVar($this->username),
                                "Password" => $this->newSoapVar($this->password)
                            ],
                            "UsernameToken"
                        ),
                    ],
                    "UsernameToken"
                ),
                "Security"
            ),
            true
        );
    }

    public function setSoapVersion(int $soapVersion): void
    {
        $this->soapVersion = $soapVersion;
    }

    public function setTrace(bool $trace): void
    {
        $this->trace = $trace;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Sets the path to the twig directory
     *
     * @param string $viewPath
     *
     * @return void
     *
     * @throws \ErrorException If the path does not exist or is unreadable
     */
    public function setViewPath(string $viewPath): void
    {
        $realPath = realpath($viewPath);

        if ($realPath === false || !is_readable($realPath)) {
            throw new \ErrorException("Directory '$viewPath' does not exist or is not readable");
        }

        $this->viewPath = $realPath;
    }

    public function setWsdlUrl(string $url): void
    {
        $this->setBaseUrl($url);
    }

    public function setWsseUrl(string $url): void
    {
        $this->wsseUrl = $url;
    }

    /**
     * Helpers
     */

    public function addFeature(int $feature): void
    {
        $this->features |= $feature;
    }

    public function hasParameters(): bool
    {
        return count($this->parameters) > 0;
    }

    /**
     * Private methods
     */

    private function newSoapVar($data, ?string $tag = null, ?string $url = null): SoapVar
    {
        $url = $url ?? $this->wsseUrl;

        if (is_string($data)) {
            $type = XSD_STRING;
        }
        else if (is_object($data) || is_array($data)) {
            $data = (object)$data;
            $type = SOAP_ENC_OBJECT;
        }
        else {
            throw new \ErrorException("Unexpected SoapVar type");
        }

        return new SoapVar($data, $type, NULL, $url, $tag, $url);
    }
}
