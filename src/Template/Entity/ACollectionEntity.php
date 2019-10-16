<?php

    namespace Kentron\Template\Entity;

    use Kentron\Entity\Entity;

    abstract class ACollectionEntity extends Entity
    {
        /**
         * Relative path to core ApiEntity or DBEntity
         * @var string
         */
        private $coreEntityClass;

        /**
         * The collection of core entities
         * @var array
         */
        private $collection = [];

        /**
         * Save the entity path
         * @param string $entityClass Absolute path to extended Entity
         */
        protected function __construct (string $entityClass)
        {
            $this->coreEntityClass = $entityClass;
        }

        /**
         * Append an entity to the collection
         * @param Entity $entity
         * @return void
         */
        final public function addEntity (Entity $entity): void
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
    }
