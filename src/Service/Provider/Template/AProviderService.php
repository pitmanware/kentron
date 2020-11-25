<?php

namespace Kentron\Service\Provider\Template;

// Entities
use Kentron\Entity\ProviderTransportEntity;

// Services
use Kentron\Service\Http\HttpService;

abstract class AProviderService
{
    /**
     * @var AProviderEntity
     */
    protected $providerEntity;

    /**
     * @var IProviderRequestService
     */
    private $providerRequestService;

    /**
     * @var IProviderResponseService
     */
    private $providerResponseService;

    protected function __construct (AProviderEntity $providerEntity)
    {
        $this->providerEntity = $providerEntity;
    }

    /**
     * Makes the request to the provider
     * @param  ProviderTransportEntity $providerTransportEntity The transport entity for the request and response data from the controller
     * @return bool                                             The success of the request
     */
    final public function makeRequest (ProviderTransportEntity $providerTransportEntity): bool
    {
        $httpEntity = $this->providerEntity->getHttpEntity();

        // Merge the transport entity into the main provider entity
        $this->providerEntity->setRequestData($providerTransportEntity->getRequestData());

        // Format the request based on the request type
        $this->providerRequestService->buildRequest($this->providerEntity);

        // TODO check if can turn provider entity into just a class
        if ($this->providerEntity->hasErrors()) {
            $providerTransportEntity->addError($this->providerEntity->getErrors());
            return false;
        }

        $method = $httpEntity->getMethod();
        $this->auditRequest(
            $httpEntity->getUrl() . (is_string($method) ? "::$method" : ""),
            $this->providerEntity->getPostDataAsString()
        );

        // Attempt connection to the provider
        if (!HttpService::run($httpEntity)) {
            $providerTransportEntity->addError($httpEntity->getErrors());
        }

        // Audit the response
        $this->auditResponse($httpEntity->getRawResponse(), $httpEntity->getStatusCode());

        if ($providerTransportEntity->hasErrors()) {
            return false;
        }

        // Format the response and make it readable for the caller system
        $providerTransportEntity->setResponseData(
            $this->providerResponseService->formatResponse($this->providerEntity)
        );

        if ($this->providerEntity->hasErrors()) {
            $providerTransportEntity->addError($this->providerEntity->getErrors());
            return false;
        }

        return true;
    }

    final public function setProviderRequestService (IProviderRequest $requestService): void
    {
        $this->providerRequestService = $requestService;
    }

    final public function setProviderResponseService (IProviderResponse $responseService): void
    {
        $this->providerResponseService = $responseService;
    }

    abstract protected function auditRequest (string $url, ?string $body): void;
    abstract protected function auditResponse (int $statusCode, ?string $body): void;
}
