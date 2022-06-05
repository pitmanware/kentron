<?php
declare(strict_types=1);

namespace Kentron\Template\Entity;

use Kentron\Template\Entity\ACoreEntity;

use Kentron\Support\Type\Type;

abstract class ACoreCollectionEntity extends ACoreEntity
{
    use TCollection;

    /**
     * Build the array of entities
     *
     * @param object|array $entityData Array of arrays or objects only
     *
     * @return void
     */
    final public function hydrateCollection($entityData): void
    {
        if (count($this->propertyMap) > 0) {
            $this->hydrate($entityData);
            return;
        }

        foreach ($entityData as $data) {
            $entity = $this->newEntity();

            if (!$entity instanceof ACoreEntity) {
                return;
            }

            if (!Type::isIterable($data)) {

                $entity->hydrate($entityData);
                $this->addEntity($entity);
                break;
            }


            $entity->hydrate($data);
            $this->addEntity($entity);
        }
    }
}
