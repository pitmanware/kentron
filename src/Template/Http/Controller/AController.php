<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Controller;

use Kentron\Entity\ProviderTransportEntity;
use Kentron\Entity\TransportEntity;

use Kentron\Template\Entity\AEntity;
use Kentron\Template\Provider\AProviderService;

/**
 * The base abstract controller
 */
abstract class AController
{
    private ProviderTransportEntity $providerTransportEntity;

    /**
     * Creates the Request and Response objects and runs access checks
     * @param TransportEntity $transportEntity The entity containing Slim objects
     */
    public function __construct(
        protected TransportEntity $transportEntity
    ) {

        $this->providerTransportEntity = new ProviderTransportEntity();
    }

    final protected function requestProvider(AProviderService $provider, ?AEntity $requestEntity = null): ?array
    {
        $this->providerTransportEntity->requestEntity = $requestEntity;

        if (!$provider->run($this->providerTransportEntity)) {
            $this->transportEntity->mergeAlerts($this->providerTransportEntity);
            $this->transportEntity->setInternalServerError();
        }

        return $this->providerTransportEntity->responseData;
    }
}
