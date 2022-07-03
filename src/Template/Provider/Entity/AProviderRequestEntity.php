<?php

namespace Kentron\Template\Provider\Entity;

use Kentron\Template\Entity\AEntity;
use Kentron\Support\Http\Http;

/**
 * Handles all data to be passed around the provider service
 */
abstract class AProviderRequestEntity extends AEntity
{
    /** The HTTP entity for the request */
    public Http $http;

    /** The request data from the controller */
    protected ?AEntity $requestData;

    /**
     * Used for setting all the relevant configuration data for the provider API
     */
    public function __construct()
    {
        $this->http = new Http();
    }

    /**
     * Getters
     */

    final public function getRequestData(): ?AEntity
    {
        return $this->requestData;
    }

    /**
     * Setters
     */

    final public function setRequestData(?AEntity $data): void
    {
        $this->requestData = $data;
    }
}
