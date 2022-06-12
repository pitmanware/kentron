<?php
declare(strict_types=1);

namespace Kentron\Entity;

use Kentron\Template\Entity\TCollection;

/**
 * Blank Entity Collection, probably should never be used
 */
class EntityCollection
{
    use TCollection;

    protected ?string $entityClass = Entity::class;
}
