<?php

namespace Kentron\Service\Provider\Template;

use Kentron\Template\Entity\AEntity;

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
     * @var mixed|null
     */
    protected $requestData;

    /**
     * Any extra details required for the provider
     * @var object|null
     */
    private $providerDetails;

    /**
     * Used for setting all the relevant configuration data for the provider API
     */
    public function __construct ()
    {
        $this->httpEntity = new HttpEntity();

        $this->httpEntity->setBaseUrl(AVariable::getProviderUrl());

        if (!$this->httpEntity->isCurl()) {
            $this->httpEntity->setUsername(AVariable::getProviderUsername());
            $this->httpEntity->setPassword(AVariable::getProviderPassword());
        }

        $this->providerDetails = AVariable::getProviderExtraDetails();
    }

    /**
     * Getters
     */

    final public function getHttpEntity (): HttpEntity
    {
        return $this->httpEntity;
    }

    final public function getProviderDetails (): ?object
    {
        return $this->providerDetails;
    }

    final public function getRequestData ()
    {
        return $this->requestData;
    }

    /**
     * Returns a stringified version of the post data for the audit
     * @return string|null
     */
    final public function getPostDataAsString (): ?string
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

    final public function setRequestData ($data): void
    {
        $this->requestData = $data;
    }
}
