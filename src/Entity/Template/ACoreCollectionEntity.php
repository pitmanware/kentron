<?php

namespace Kentron\Entity\Template;

use Kentron\Entity\Template\{ACollectionEntity, ACoreEntity};

use Kentron\Service\Type;

abstract class ACoreCollectionEntity extends ACoreEntity
{
    /**
     * The core entity collection
     *
     * @var array
     */
    private $coreCollection = [];

    private $coreEntityClass;

    /**
     * Overridden to expect ACollectionEntity
     *
     * @param ACollectionEntity $collectionEntity This becomes the core entity
     * @param string|null       $coreEntityClass  This is the relative path for the collected core entity
     *
     * @throws \InvalidArgumentException If the core class path is invalid
     */
    protected function __construct(ACollectionEntity $collectionEntity, ?string $coreEntityClass = null)
    {
        parent::__construct($collectionEntity);

        if (is_string($coreEntityClass) && !is_subclass_of($coreEntityClass, ACoreEntity::class)) {
            throw new \InvalidArgumentException("$coreEntityClass must be an instance of " . ACoreEntity::class);
        }

        $this->coreEntityClass = $coreEntityClass;
    }

    /**
     * Overrides parent to return the collection
     *
     * @return ACollectionEntity
     */
    final public function getRootEntity(): ACollectionEntity
    {
        return parent::getRootEntity();
    }

    /**
     * Build the array of entities
     *
     * @param object|array $entityData Array of arrays or objects only
     *
     * @return void
     */
    final public function buildCollection($entityData): void
    {
        if (!Type::isIterable($entityData)) {
            return;
        }

        if (count($this->propertyMap) > 0) {
            $this->build($entityData);
            return;
        }

        foreach ($entityData as $data) {
            $entity = $this->getNewCoreEntity();

            if (is_null($entity)) {
                continue;
            }

            if (!Type::isIterable($data)) {

                $entity->build($entityData);
                $this->addEntity($entity);
                break;
            }

            $entity->build($data);
            $this->addEntity($entity);
        }
    }

    /**
     * Return a new core entity if the class path is set
     *
     * @return ACoreEntity|null
     */
    final public function getNewCoreEntity(): ?ACoreEntity
    {
        if (is_null($this->coreEntityClass)) {
            return null;
        }

        $coreEntityClass = $this->coreEntityClass;
        return new $coreEntityClass();
    }

    /**
     * Append an ACoreEntity to the core collection
     *
     * @param ACoreEntity $coreEntity
     */
    final public function addEntity(ACoreEntity $coreEntity): void
    {
        $this->coreCollection[] = $coreEntity;
        $this->getRootEntity()->addEntity($coreEntity->getRootEntity());
    }

    /**
     * Generator for iterating through the root entities
     *
     * @return iterable
     */
    final public function iterateEntities(): iterable
    {
        yield from $this->getRootEntity()->iterateEntities();
    }

    /**
     * Generator for iterating through the core entities
     *
     * @return iterable
     */
    final public function iterateCoreEntities(): iterable
    {
        foreach ($this->coreCollection as $coreEntity) {
            yield $coreEntity;
        }
    }

    public function normalise(): array
    {
        if ($this->getRootEntity()->isValidMethod(__FUNCTION__)) {
            return $this->getRootEntity()->{__FUNCTION__}();
        }

        $normalised = [];

        foreach ($this->iterateCoreEntities() as $coreEntity) {
            $normalised[] = $coreEntity->normalise();
        }

        return $normalised;
    }

    /**
     * Run a function for every core entity in the collection
     *
     * @param array $methods    The methods to call on all the entities
     * @param bool  $flatten    If the results should be reduced to a single dimension
     * @param array $conditions Comparisons to be made against the result
     *
     * @return array
     */
    final public function map(array $methods, bool $flatten = false, ?array $conditions = null, bool $namedIndexes = false): array
    {
        return $this->getRootEntity()->map($methods, $flatten, $conditions, $namedIndexes);
    }

    /**
     * Similar to map, except it iterates through entities that pass all conditions
     *
     * @param array $conditions
     *
     * @return AEntity[]
     */
    final public function filter(array $conditions): iterable
    {
        yield from $this->getRootEntity()->filter($conditions);
    }

    /**
     * Returns first entity that passes the condition
     *
     * @param array $conditions
     * @param bool  $and        Do OR or AND operations for filters
     *
     * @return AEntity|null
     */
    final public function filterFirst (array $conditions, bool $and = true): ?AEntity
    {
        return $this->getRootEntity()->filterFirst($conditions, $and);
    }

    /**
     * Group the entities together by the return of a given function and iterate them
     *
     * @param string $method
     *
     * @return AEntity[]
     */
    final public function groupBy(string $method): iterable
    {
        yield from $this->getRootEntity()->groupBy($method);
    }

    /**
     * Get the first Entity in the collection and remove it
     *
     * @return Entity|null
     */
    final public function shiftCoreEntity(): ?ACoreEntity
    {
        $this->getRootEntity()->shiftEntity();
        return array_shift($this->coreCollection);
    }

    /**
     * Return the last Entity in the collection and remove it
     *
     * @return Entity|null
     */
    final public function popCoreEntity(): ?ACoreEntity
    {
        $this->getRootEntity()->popEntity();
        return array_pop($this->coreCollection);
    }
}
