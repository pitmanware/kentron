<?php

namespace Kentron\Service\Provider\Template;

use Kentron\Entity\Template\AEntity;

abstract class AProviderResponseEntity extends AEntity
{
    abstract protected function saveError (int $statusCode, string $message): void;
}
