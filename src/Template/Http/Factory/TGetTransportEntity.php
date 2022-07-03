<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Factory;

use Kentron\Entity\TransportEntity;

trait TGetTransportEntity
{
    /**
     * Get the highest level transport Entity
     *
     * @return TransportEntity
     */
    abstract protected static function getTransportEntity(): TransportEntity;
}
