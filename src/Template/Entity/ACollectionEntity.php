<?php

namespace Kentron\Template\Entity;

use Kentron\Service\Assert;

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

        foreach ($this->iterateEntities() as $entity) {

            foreach ($methods as $key => $method) {

                if (!$entity->isValidMethod($method)) {
                    continue;
                }
                $mappedData = $entity->{$method}();

                if (is_array($conditions)) {

                    foreach ($conditions as [$operand, $assertion, $result]) {

                        if (!$entity->isValidMethod($operand)) {
                            continue;
                        }
                        $value = $entity->{$operand}();

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
        foreach ($this->iterateEntities() as $entity) {

            if (!is_array($conditions[0])) {
                $conditions = [$conditions];
            }

            foreach ($conditions as [$method, $assertion, $result]) {

                if (!$entity->isValidMethod($method)) {
                    continue 2;
                }
                $value = $entity->{$method}();

                if (!Assert::$assertion($value, $result)) {
                    continue 2;
                }
            }

            yield $entity;
        }
    }
}
