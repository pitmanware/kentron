<?php

namespace Kentron\Template\Provider;

// Entities
use Kentron\Template\Provider\Entity\AProviderEntity;
use Kentron\Entity\ProviderTransportEntity;
use Kentron\Support\Http\Http;

abstract class AProviderService
{
    /** The specific provider entity */
    protected AProviderEntity $providerEntity;

    /** The specific provider request handler */
    private IProviderRequestService $providerRequestService;

    /** The specific provider response handler */
    private IProviderResponseService $providerResponseService;

    protected function __construct(AProviderEntity $providerEntity)
    {
        $this->providerEntity = $providerEntity;
    }

    /**
     * Makes the request to the provider
     * @param  ProviderTransportEntity $providerTransportEntity The transport entity for the request and response data from the controller
     * @return bool                                             The success of the request
     */
    final public function run(ProviderTransportEntity $providerTransportEntity): bool
    {
        $http = $this->providerEntity->http;

        // Merge the transport entity into the main provider entity
        $this->providerEntity->setRequestData($providerTransportEntity->requestEntity);

        // Format the request based on the request type
        $this->providerRequestService->buildRequest($this->providerEntity);

        // TODO check if can turn provider entity into just a class
        if ($this->providerEntity->hasErrors()) {
            $providerTransportEntity->mergeAlerts($this->providerEntity);
            return false;
        }

        $this->auditRequest($http);

        // Attempt connection to the provider
        if (!$http->run()) {
            $providerTransportEntity->addError($http->errors);
        }

        // Audit the response
        $this->auditResponse($http);

        if ($providerTransportEntity->hasErrors()) {
            return false;
        }

        // Format the response and make it readable for the caller system
        $providerTransportEntity->responseData =
            $this->providerResponseService->formatResponse($this->providerEntity)
        ;

        if ($this->providerEntity->hasErrors()) {
            $providerTransportEntity->mergeAlerts($this->providerEntity);
            return false;
        }

        return true;
    }

    final public function setProviderRequestService(IProviderRequestService $requestService): void
    {
        $this->providerRequestService = $requestService;
    }

    final public function setProviderResponseService(IProviderResponseService $responseService): void
    {
        $this->providerResponseService = $responseService;
    }

    abstract protected function auditRequest(Http $http): void;
    abstract protected function auditResponse(Http $http): void;
}
