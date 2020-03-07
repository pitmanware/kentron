<?php

namespace Kentron\Facade;

use Kentron\Service\Xml as Xml\Service;

use Kentron\Exception\XmlFormatException;

final class Soap extends \SoapClient
{
    /**
     * Target action method
     * @var string $action
     */
    private $action;

    /**
     * Any errors returned from the Soap client
     * @var array $errors
     */
    private $errors = [];

    /**
     * The extracted XML response
     * @var null|object
     */
    private $extractedXml = null;

    /**
     * The raw XML response
     * @var string
     */
    private $rawXml = "";

    /**
     * The WSDL URL
     * @var string
     */
    private $wsdl;

    /**
     * The SOAP version, default 1.2
     * @var int
     */
    private $version = SOAP_1_2;

    public function __construct (string $wsdl, ?int $version = null)
    {
        $this->wsdl = $wsdl;

        if (is_int($version)) {
            $this->version = $version;
        }

        parent::__construct(
            $this->wsdl,
            ["soap_version" => $this->version]
        );
    }

    public function setAction (string $action): void
    {
        $this->action = $action;
    }

    public function requestRaw (string $xml, string $action, int $one_way = 0): bool
    {
        try {
            $this->rawXml = parent::__doRequest(
                $xml,
                $this->wsdl,
                $action,
                $this->version,
                $one_way
            );
        }
        catch (\SoapFault $fault) {
            $this->errors[] = "Fault SOAP: $action: faultcode: $fault->faultcode, faultstring: $fault->faultstring";
        }
        catch (\Exception $exception) {
            $this->errors[] = "Exception SOAP: $action: $exception";
        }

        if (isset($this->__soap_fault) && !is_null($this->__soap_fault)) {
            // This is where the exception from __doRequest is stored
            $this->errors[] = "Fault SOAP __doRequest: $action: faultcode: $fault->faultcode, faultstring: $fault->faultstring";
        }

        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }

    public function request (): bool
    {
        //TODO use SoapClient call method
    }

    public function extract (): ?object
    {
        try {
            $this->extractedXml = Xml\Service::extractSoap($this->rawXml);
        }
        catch (\XmlFormatException $ex) {
            $this->errors[] = $ex->getMessage();
        }

        return $this->extractedXml;
    }
}
