<?php

    namespace Kentron\Facade;

    use Kentron\Proxy\Xml as XmlProxy;

    use Kentron\Exception\XmlFormatException;

    final class Soap extends \SoapClient
    {
        public $errors = [];

        public $extractedXml = null;

        public $rawXml = "";

        private $targetUrl;

        private $version = SOAP_1_2;

        public function __construct (string $targetUrl, ?int $version = null)
        {
            $this->targetUrl = $targetUrl;

            if (is_int($version)) {
                $this->version = $version;
            }

            parent::__construct(
                $this->targetUrl,
                ["soap_version" => $this->version]
            );
        }

        public function request (string $xml, string $action, int $one_way = 0): bool
        {
            try {
                $this->rawXml = parent::__doRequest(
                    $xml,
                    $this->targetUrl,
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

        public function extract (): ?object
        {
            try {
                $this->extractedXml = XmlProxy::extractSoap($this->rawXml);
            }
            catch (\XmlFormatException $ex) {
                $this->errors[] = $ex->getMessage();
            }

            return $this->extractedXml;
        }
    }
