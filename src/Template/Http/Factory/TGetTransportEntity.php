<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Factory;

use Kentron\Entity\TransportEntity;
use Kentron\Template\Store\AppStore;

trait TGetTransportEntity
{
    /**
     * Get the transport Entity
     *
     * @return TransportEntity
     */
    protected function getTransportEntity(): TransportEntity
    {
        return AppStore::getTransportEntity();
    }
}
