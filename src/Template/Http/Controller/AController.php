<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Controller;

use Kentron\Entity\ProviderTransportEntity;
use Kentron\Entity\TransportEntity;
use Kentron\Enum\EStatusCode;
use Kentron\Template\Entity\AEntity;
use Kentron\Template\Provider\Service\AProviderService;

/**
 * The base abstract controller
 */
abstract class AController
{
    /**
     * @param TransportEntity $transportEntity The entity containing request and response data
     */
    public function __construct(
        protected TransportEntity $transportEntity
    ) {}

    final protected function requestProvider(AProviderService $provider, ?AEntity $requestEntity = null): ?array
    {
        $providerTransportEntity = new ProviderTransportEntity();
        $providerTransportEntity->requestEntity = $requestEntity;

        if (!$provider->run($providerTransportEntity)) {
            $this->transportEntity->mergeAlerts($providerTransportEntity);
            $this->transportEntity->setStatusCode(EStatusCode::CODE_500);
        }

        return $providerTransportEntity->responseData;
    }
}
