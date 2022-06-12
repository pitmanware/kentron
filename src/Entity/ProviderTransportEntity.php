<?php
declare(strict_types=1);

namespace Kentron\Entity;

use Kentron\Template\Entity\AEntity;

final class ProviderTransportEntity extends AEntity
{
    public ?AEntity $requestEntity = null;
    public ?array $responseData = null;
}
