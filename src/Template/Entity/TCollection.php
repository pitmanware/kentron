<?php
declare(strict_types=1);

namespace Kentron\Template\Entity;

use Kentron\Support\Assert;
use Kentron\Enum\EAssertion;

use \Error;
use \InvalidArgumentException;
use ReflectionClass;
use \UnexpectedValueException;

trait TCollection
{
    /**
     * The entity collection
     * @var AEntity[]
     */
    protected array $collection = [];

    protected ?string $entityClass = null;

    /**
     * @param string|null $entityClass This is the relative path for the collected core entity
     *
     * @throws InvalidArgumentException If the core class path is invalid
     */
    public function __construct(?string $entityClass = null)
    {
        if (!is_null($entityClass)) {
            if (!class_exists($entityClass)) {
                throw new InvalidArgumentException("{$entityClass} does not exist");
            }
            else if (!is_subclass_of($entityClass, AEntity::class)) {
                throw new InvalidArgumentException("{$entityClass} must extend " . AEntity::class);
            }

            $this->entityClass = $entityClass;
        }
    }

    /**
     * Return a new core entity if the class path is set
     *
     * @return AEntity
     *
     * @throws Error If the entity class is not set
     */
    public function newEntity(): AEntity
    {
        if (is_null($this->entityClass)) {
            throw new Error("Entity class not set on " . $this::class);
        }

        $entityClass = $this->entityClass;
        return new $entityClass(...func_get_args());
    }

    /**
     * Get an AEntity by its index in the collection
     *
     * @param int $index
     *
     * @return AEntity|null
     */
    public function getEntity(int $index): ?AEntity
    {
        return $this->collection[$index] ?? null;
    }

    /**
     * Append an AEntity to the core collection
     *
     * @param AEntity $entity
     */
    public function addEntity(AEntity $entity): void
    {
        $this->collection[] = $entity;
    }

    /**
     * Append a new AEntity to the core collection and return it
     *
     * @return AEntity $entity
     */
    public function addNewEntity(): AEntity
    {
        $entity = $this->newEntity(...func_get_args());
        $this->collection[] = $entity;
        return $entity;
    }

    /**
     * Generator for iterating through the entities
     *
     * @return AEntity[]
     */
    public function iterateEntities(): iterable
    {
        foreach ($this->collection as $index => $entity) {
            yield $index => $entity;
        }
    }

    /**
     * Opposite of the hydrate method
     *
     * @param boolean $allowNullable If false, ignores null values
     *
     * @return array
     */
    public function normalise(bool $allowNullable = true): array
    {
        $normalised = [];

        foreach ($this->iterateEntities() as $entity) {
            if ($entity instanceof ACoreEntity) {
                $normalised[] = $entity->normalise($allowNullable);
            }
        }

        return $normalised;
    }

    /**
     * Run a function for every core entity in the collection
     *
     * @param array $methodsOrProperties    The methods to call on all the entities
     * @param bool  $flatten    If the results should be reduced to a single dimension
     * @param array $conditions Comparisons to be made against the result
     *
     * @return array
     */
    public function map(array $methodsOrProperties, bool $flatten = false, ?array $conditions = null, bool $namedIndexes = false): array
    {
        $map = [];
        $index = 0;

        foreach ($this->iterateEntities() as $entity) {

            foreach ($methodsOrProperties as $key => $methodOrProperty1) {

                if ($entity->isValidMethod($methodOrProperty1)) {
                    $mappedData = $entity->{$methodOrProperty1}();
                }
                else if ($entity->isValidProperty($methodOrProperty1)) {
                    $mappedData = $entity->{$methodOrProperty1};
                }
                else {
                    continue;
                }

                if (is_array($conditions)) {

                    foreach ($conditions as $condition) {

                        if (is_callable($condition)) {
                            $condition($entity);
                        }
                        else {
                            [$methodOrProperty2, $assertion, $result] = $condition;

                            if ($entity->isValidMethod($methodOrProperty2)) {
                                $value = $entity->{$methodOrProperty2}();
                            }
                            else if ($entity->isValidProperty($methodOrProperty2)) {
                                $value = $entity->{$methodOrProperty2};
                            }
                            else {
                                continue;
                            }

                            if (!Assert::parseOperator($assertion)($value, $result)) {
                                continue 3;
                            }
                        }
                    }
                }

                if ($namedIndexes) {
                    $map[$index][$methodOrProperty1] = $mappedData;
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
     *
     * @param array|callable[] $conditions Format: `[[<method to call on the entity>, <Asset method to test with>, <Expected result>], ...]`
     *                          or `[callable, ...]`
     *
     * @example - `filter([["getID", "same", 1]])`
     *
     * @return AEntity[]
     *
     * @throws UnexpectedValueException If the assertion is missing
     */
    public function filter(array $conditions): iterable
    {
        foreach ($this->iterateEntities() as $entity) {

            if (!is_array($conditions[0])) {
                $conditions = [$conditions];
            }

            foreach ($conditions as $key => $condition) {

                if (is_callable($condition)) {
                    $condition($entity);
                }
                else {
                    if (is_string($key)) {
                        $methodOrProperty = $key;
                        $assertion = EAssertion::Same;
                        $result = $condition;
                    }
                    else {
                        [$methodOrProperty, $assertion, $result] = $condition;
                    }

                    if (is_null($assertion)) {
                        throw new UnexpectedValueException("Missing assertion in filter");
                    }

                    if ($entity->isValidMethod($methodOrProperty)) {
                        $value = $entity->{$methodOrProperty}();
                    }
                    else if ($entity->isValidProperty($methodOrProperty)) {
                        $value = $entity->{$methodOrProperty};
                    }
                    else {
                        continue 2;
                    }

                    if (!Assert::parseOperator($assertion)($value, $result)) {
                        continue 2;
                    }
                }
            }

            yield $entity;
        }
    }

    /**
     * Returns first entity that passes the condition
     *
     * @param array $conditions
     * @param bool $and Do OR or AND operations for filters
     *
     * @return AEntity|null
     */
    public function filterFirst(array $conditions, bool $and = true): ?AEntity
    {
        foreach ($this->filter($conditions, $and) as $entity) {
            return $entity;
        }

        return null;
    }

    /**
     * Group the entities together by the return of a given function and iterate them
     *
     * @param string $methodOrProperty
     *
     * @return AEntity[]
     */
    public function groupBy(string $methodOrProperty): iterable
    {
        $grouped = [];

        foreach ($this->iterateEntities() as $entity) {

            if ($entity->isValidMethod($methodOrProperty)) {
                $value = $entity->{$methodOrProperty}();
            }
            else if ($entity->isValidProperty($methodOrProperty)) {
                $value = $entity->{$methodOrProperty};
            }
            else {
                continue ;
            }

            $grouped[$value][] = $entity;
        }

        foreach ($grouped as $key => $entity) {
            yield $key => $entity;
        }
    }

    /**
     * Get the first AEntity in the collection and remove it
     *
     * @return AEntity|null
     */
    public function shiftEntity(): ?AEntity
    {
        return array_shift($this->collection);
    }

    /**
     * Return the last AEntity in the collection and remove it
     *
     * @return AEntity|null
     */
    public function popEntity(): ?AEntity
    {
        return array_pop($this->collection);
    }

    /**
     * Return the collection
     *
     * @return AEntity[]
     */
    public function getEntities(): array
    {
        return $this->collection;
    }

    /**
     * Returns the amound of entities saved in the collection
     *
     * @return int
     */
    final public function countEntities(): int
    {
        return count($this->collection);
    }

    /**
     * Return the last AEntity in the collection
     *
     * @return AEntity|null
     */
    public function getLast(): ?AEntity
    {
        return $this->collection[array_key_last($this->collection)] ?? null;
    }

    /**
     * Return the last AEntity in the collection
     *
     * @return AEntity|null
     */
    public function getFirst(): ?AEntity
    {
        return $this->collection[array_key_first($this->collection)] ?? null;
    }

    /**
     * Translate the values of properties from one AEntity to another with the same properties
     *
     * @param AEntity|self $aEntity The target entity
     * @param bool $clobber Set to true to replace any initialised variables on the target entity
     *
     * @return AEntity|self The translated entity
     *
     * @throws Error If it could not create a reflection class for either $this or $aEntity
     */
    public function translate(AEntity $aEntity, bool $clobber = true): AEntity
    {
        if (
            ($aEntity instanceof ACollectionEntity) ||
            ($aEntity instanceof ACoreCollectionEntity) ||
            (in_array(TCollection::class, (new ReflectionClass($aEntity))->getTraitNames()))
        ) {
            foreach ($this->iterateEntities() as $entity) {
                $entity->translate($aEntity->addNewEntity(), $clobber);
            }
        }
        else if (($this instanceof AEntity) && ($aEntity instanceof AEntity)) {
            parent::translate($aEntity, $clobber);
        }
        else {
            throw new Error("Could not translate entity on TCollection");
        }

        return $aEntity;
    }
}
