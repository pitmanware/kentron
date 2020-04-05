<?php

namespace Kentron\Template\Entity;

abstract class ACollectionEntity extends AEntity
{
    /**
     * Class path to the collected entity
     * @var string
     */
    private $entityClass;

    /**
     * The collection of core entities
     * @var array
     */
    private $collection = [];

    /**
     * Save the entity path
     * @param string $entityClass Absolute path to extended AEntity
     */
    public function __construct (?string $entityClass = null)
    {
        if (!is_null($entityClass) && !is_subclass_of($entityClass, AEntity::class)) {
            throw new \InvalidArgumentException("$entityClass must be an instance of " . AEntity::class);
        }

        $this->entityClass = $entityClass;
    }

    /**
     * Gets a new instance of the core AEntity
     * @return AEntity|null
     */
    final public function getNewEntity (): ?AEntity
    {
        if (is_null($this->entityClass)) {
            return null;
        }

        $entityClass = $this->entityClass;
        return new $entityClass();
    }

    /**
     * Get an AEntity by its index in the collection
     * @param  int         $index
     * @return AEntity|null
     */
    final public function getEntity (int $index): ?AEntity
    {
        return $this->collection[$index] ?? null;
    }

    /**
     * Return the collection
     * @return array
     */
    final public function getEntities (): array
    {
        return $this->collection;
    }

    /**
     * Append an entity to the collection
     * @param  AEntity $entity
     * @return void
     */
    final public function addEntity (AEntity $entity): void
    {
        $this->collection[] = $entity;
    }

    /**
     * Returns the amound of entities saved in the collection
     * @return int
     */
    final public function countEntities (): int
    {
        return count($this->collection);
    }

    /**
     * Generator for iterating through the collection
     * @return iterable
     */
    final public function iterateEntities (): iterable
    {
        foreach ($this->collection as $entity) {
            yield $entity;
        }
    }

    /**
     * Get the first AEntity in the collection and remove it
     * @return AEntity|null
     */
    final public function shiftEntity (): ?AEntity
    {
        return array_shift($this->collection);
    }

    /**
     * Return the last AEntity in the collection and remove it
     * @return AEntity|null
     */
    final public function popEntity (): ?AEntity
    {
        return array_pop($this->collection);
    }
}
