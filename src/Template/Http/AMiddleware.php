<?php
declare(strict_types=1);

namespace Kentron\Template\Http;

use Kentron\Entity\TransportEntity;
use Kentron\Template\AClass;

/**
 * The base abstract middleware
 */
abstract class AMiddleware extends AClass
{
    /**
     * Creates the Request and Response objects and runs access checks
     * @param TransportEntity $transportEntity The entity containing Slim objects
     */
    public function __construct(
        protected TransportEntity $transportEntity
    ) {}

    /**
     * All middlewares must be able to be run by the router
     * @param TransportEntity   $transportEntity The custom transport object
     * @return void
     */
    final public function run(TransportEntity $transportEntity): void
    {
        $this->transportEntity = $transportEntity;

        $this->customRun();
    }

    /**
     * Abstract run function for any sub classes to perform initialisation scripts
     * @return void
     */
    abstract protected function customRun(): void;
}
