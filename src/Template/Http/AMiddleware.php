<?php

namespace Kentron\Template\Http;

use Kentron\Entity\TransportEntity;

/**
 * The base abstract middleware
 */
abstract class AMiddleware
{
    /**
     * @var TransportEntity
     */
    protected $transportEntity;

    /**
     * All middlewares must be able to be run by the router
     * @param TransportEntity   $transportEntity The custom transport object
     * @return void
     */
    final public function run (TransportEntity $transportEntity): void
    {
        $this->transportEntity = $transportEntity;

        $this->customRun();
    }

    /**
     * Abstract run function for any sub classes to perform initialisation scripts
     * @return void
     */
    abstract protected function customRun (): void;
}
