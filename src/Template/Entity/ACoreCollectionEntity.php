<?php

namespace Kentron\Template\Entity;

use Kentron\Template\Entity\{ACollectionEntity, ACoreEntity};

use Kentron\Service\{Type, Assert};

abstract class ACoreCollectionEntity extends ACoreEntity
{
    /**
     * The core entity collection
     * @var array
     */
    private $coreCollection = [];

    private $coreEntityClass;

    /**
     * Overridden to expect ACollectionEntity
     * @param ACollectionEntity $collectionEntity This becomes the core entity
     * @param string|null       $coreEntityClass  This is the relative path for the collected core entity
     * @throws \InvalidArgumentException If the core class path is invalid
     */
    protected function __construct (ACollectionEntity $collectionEntity, ?string $coreEntityClass = null)
    {
        parent::__construct($collectionEntity);

        if (is_string($coreEntityClass) && !is_subclass_of($coreEntityClass, ACoreEntity::class)) {
            throw new \InvalidArgumentException("$coreEntityClass must be an instance of " . ACoreEntity::class);
        }

        $this->coreEntityClass = $coreEntityClass;
    }

    /**
     * Build the array of entities
     * @param object|array $entityData Array of arrays or objects only
     * @return void
     */
    final public function buildCollection ($entityData): void
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

    final public function getNewCoreEntity (): ?ACoreEntity
    {
        if (is_null($this->coreEntityClass)) {
            return null;
        }

        $coreEntityClass = $this->coreEntityClass;
        return new $coreEntityClass();
    }

    /**
     * Append an ADBEntity or AAPIEntity to the core collection
     * @param ACoreEntity $coreEntity
     */
    final public function addEntity (ACoreEntity $coreEntity): void
    {
        $this->coreCollection[] = $coreEntity;
        $this->rootEntity->addEntity($coreEntity->getRootEntity());
    }

    /**
     * Generator for iterating through the root entities
     * @return iterable
     */
    final public function iterateEntities (): iterable
    {
        yield from $this->rootEntity->iterateEntities();
    }

    /**
     * Generator for iterating through the core entities
     * @return iterable
     */
    final public function iterateCoreEntities (): iterable
    {
        foreach ($this->coreCollection as $coreEntity) {
            yield $coreEntity;
        }
    }

    public function normalise (): array
    {
        $normalised = [];

        foreach ($this->iterateCoreEntities() as $coreEntity) {
            $normalised[] = $coreEntity->normalise();
        }

        return $normalised;
    }

    /**
     * Run a function for every core entity in the collection
     * @param  array $methods    The methods to call on all the entities
     * @param  bool  $flatten    If the results should be reduced to a single dimension
     * @param  array $conditions Comparisons to be made against the result
     * @return array
     */
    final public function map (array $methods, bool $flatten = false, ?array $conditions = null, bool $namedIndexes = false): array
    {
        $map = [];
        $index = 0;

        foreach ($this->iterateCoreEntities() as $coreEntity) {

            foreach ($methods as $key => $method) {

                if (!$this->isValidMethod($coreEntity->getRootEntity(), $method)) {
                    continue;
                }
                $mappedData = $coreEntity->getRootEntity()->{$method}();

                if (is_array($conditions)) {

                    foreach ($conditions as [$operand, $assertion, $result]) {

                        if (!$this->isValidMethod($coreEntity->getRootEntity(), $operand)) {
                            continue;
                        }
                        $value = $coreEntity->getRootEntity()->{$operand}();

                        if (!Assert::$assertion($value, $result)) {
                            continue 3;
                        }
                    }
                }

                if ($namedIndexes) {
                    $map[$index][$method] = $mappedData;
                }
                else {
                    $map[$index][$key] = $mappedData;
                }
            }

            $index++;
        }

        return (!!$map && $flatten) ? array_merge_recursive(...$map) : $map;
    }

    /**
     * Similar to map, except it iterates through entities that pass all conditions
     * @param  array    $conditions
     * @return iterable
     */
    final public function filter (array $conditions): iterable
    {
        foreach ($this->iterateCoreEntities() as $coreEntity) {

            if (!is_array($conditions[0])) {
                $conditions = [$conditions];
            }

            foreach ($conditions as [$method, $assertion, $result]) {

                if (!$this->isValidMethod($coreEntity->getRootEntity(), $method)) {
                    continue 2;
                }
                $value = $coreEntity->getRootEntity()->{$method}();

                if (!Assert::$assertion($value, $result)) {
                    continue 2;
                }
            }

            yield $coreEntity;
        }
    }

    /**
     * Get the first Entity in the collection and remove it
     * @return Entity|null
     */
    final public function shiftCoreEntity (): ?ACoreEntity
    {
        $this->rootEntity->shiftEntity();
        return array_shift($this->coreCollection);
    }

    /**
     * Return the last Entity in the collection and remove it
     * @return Entity|null
     */
    final public function popCoreEntity (): ?ACoreEntity
    {
        $this->rootEntity->popEntity();
        return array_pop($this->coreCollection);
    }
}
