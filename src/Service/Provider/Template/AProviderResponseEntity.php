<?php

namespace Kentron\Service\Provider\Template;

use Kentron\Template\Entity\AEntity;

abstract class AProviderResponseEntity extends AEntity
{
    abstract protected function saveError (int $statusCode, string $message): void;
}
