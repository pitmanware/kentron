<?php

namespace Kentron\Service\Provider\Template;

use Kentron\Entity\Template\AEntity;
use Kentron\Store\Variable\AVariable;

abstract class AProviderRequestEntity extends AEntity
{
    /** @var string */
    protected $url;

    /** @var string */
    protected $uri;

    protected function __construct(string $uri = "")
    {
        $this->url = AVariable::getProviderUrl();
        $this->uri = trim($uri, "/");
    }

    final public function getUrl(): string
    {
        return $this->url;
    }

    final public function getUri(): string
    {
        return $this->uri;
    }
}
