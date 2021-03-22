<?php

namespace Kentron\Template;

use Kentron\Entity\TransportEntity;
use Kentron\Store\Variable\AVariable;

/**
 * The inital application class, injected into the controllers
 */
trait TApp
{
    protected $transportEntityClass = TransportEntity::class;

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

    final public function setTransportEntityClass(string $transportEntityClass): void
    {
        if (!is_a($transportEntityClass, TransportEntity::class)) {
            throw new \Error("{$transportEntityClass} does not extend from {$this->transportEntityClass}");
        }
    }

    final public function resetTransportEntity(): void
    {
        $transportEntityClass = $this->transportEntityClass;
        AVariable::setTransportEntity(new $transportEntityClass());
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
