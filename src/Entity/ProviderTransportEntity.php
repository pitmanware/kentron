<?php

namespace Kentron\Entity;

use Kentron\Entity\Template\AEntity;

final class ProviderTransportEntity extends AEntity
{
    private $requestData;
    private $responseData;

    /**
     * Getters
     */

    public function getRequestData(): ?object
    {
        return $this->requestData;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    /**
     * Setters
     */

    public function setRequestData(?object $requestData = null): void
    {
        $this->requestData = $requestData;
    }

    public function setResponseData(?array $responseData = null): void
    {
        $this->responseData = $responseData;
    }
}
