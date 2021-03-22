<?php

namespace Kentron\Template\Http;

use Kentron\Entity\TransportEntity;

/**
 * The base abstract controller
 */
abstract class AController
{
    /**
     * The application specific config data
     * @var array
     */
    protected $appConfig;

    /**
     * The custom transport object
     * @var TransportEntity
     */
    protected $transportEntity;

    /**
     * Creates the Request and Response objects and runs access checks
     * @param TransportEntity $transportEntity The entity containing Slim objects
     */
    public function __construct(TransportEntity $transportEntity)
    {
        $this->transportEntity = $transportEntity;
    }
}
