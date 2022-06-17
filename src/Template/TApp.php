<?php
declare(strict_types=1);

namespace Kentron\Template;

use \Error;

use Kentron\Entity\TransportEntity;
use Kentron\Template\Store\App;

/**
 * The inital application class, injected into the controllers
 */
trait TApp
{
    private string $transportEntityClass = TransportEntity::class;

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

    private function setTransportEntityClass(string $transportEntityClass): void
    {
        if (!is_a($transportEntityClass, TransportEntity::class, true)) {
            throw new Error("{$transportEntityClass} does not extend from {$this->transportEntityClass}");
        }

        $this->transportEntityClass = $transportEntityClass;
    }

    private function resetTransportEntity(): void
    {
        $transportEntityClass = $this->transportEntityClass;
        App::setTransportEntity(new $transportEntityClass());
    }

    /**
     * Load in the env file
     *
     * @return void
     */
    abstract protected function loadEnvironment(): void;

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
