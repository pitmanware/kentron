<?php

namespace Kentron\Template;

use Kentron\Entity\TransportEntity;
use Kentron\Store\Variable\AVariable;

/**
 * The inital application class, injected into the controllers
 */
trait TApp
{
    /**
     * Set all things that allow for an app reset
     *
     * @return void
     */
    public function reset(): void
    {
        $this->resetStores();
        $this->resetTransportEntity();
    }

    abstract public function resetStores(): void;

    final public function resetTransportEntity(): void
    {
        AVariable::setTransportEntity(new TransportEntity());
    }

    /**
     * Load in the config file
     *
     * @return void
     */
    abstract protected function loadConfig(): void;

    /**
     * Sets the variables in the Variable Store
     *
     * @return void
     */
    abstract protected function loadVariables(): void;

    /**
     * Set up the database connection
     *
     * @return void
     */
    abstract protected function bootOrm(): void;

    /**
     * Load in all the routes
     *
     * @return void
     */
    abstract protected function bootRouter(): void;
}
