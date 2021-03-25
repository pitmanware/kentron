<?php

namespace Kentron\Service\Provider\Template;

use Kentron\Entity\Template\AEntity;

use Kentron\Service\Http\Entity\HttpEntity;
use Kentron\Store\Variable\AVariable;

/**
 * Handles all data to be passed around the provider service
 */
abstract class AProviderEntity extends AEntity
{
    /**
     * The HTTP entity for the request
     * @var HttpEntity
     */
    protected $httpEntity;

    /**
     * The request data from the controller
     * @var object|null
     */
    protected $requestData;

    /**
     * Used for setting all the relevant configuration data for the provider API
     */
    public function __construct()
    {
        $this->resetHttpEntity();
    }

    /**
     * Getters
     */

    final public function getHttpEntity(): HttpEntity
    {
        return $this->httpEntity;
    }

    final public function getRequestData(): ?object
    {
        return $this->requestData;
    }

    /**
     * Returns a stringified version of the post data for the audit
     * @return string|null
     */
    final public function getPostDataAsString(): ?string
    {
        $postData = $this->httpEntity->getPostData();

        if (is_string($postData) || is_null($postData)) {
            return $postData;
        }

        return json_encode($postData);
    }

    /**
     * Setters
     */

    final public function setRequestData(?object $data): void
    {
        $this->requestData = $data;
    }

    public function resetHttpEntity(): void
    {
        $this->httpEntity = new HttpEntity();

        $this->httpEntity->setBaseUrl(AVariable::getProviderUrl());

        if(!$this->httpEntity->isCurl()) {
            $this->httpEntity->setUsername(AVariable::getProviderUsername());
            $this->httpEntity->setPassword(AVariable::getProviderPassword());
        }
    }
}
