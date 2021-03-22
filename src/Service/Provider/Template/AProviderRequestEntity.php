<?php

namespace Kentron\Service\Provider\Template;

use Kentron\Entity\Template\AEntity;

abstract class AProviderRequestEntity extends AEntity
{
    protected $uri;

    protected function __construct(string $uri = "")
    {
        $this->uri = "/" . trim($uri, "/");
    }

    final public function getUri(): string
    {
        return $this->uri;
    }
}
