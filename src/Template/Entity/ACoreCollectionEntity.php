<?php

    namespace Kentron\Template\Entity;

    use Kentron\Template\Entity\{ACollectionEntity,ACoreEntity};

    abstract class ACoreCollectionEntity extends ACollectionEntity
    {
        /**
         * Overridden to expect ADBEntity or AApiEntity
         * @param string $entityClass Absolute path to extended ACoreEntity
         * @throws \TypeError|\Error
         */
        protected function __construct (string $entityClass)
        {
            if (!is_subclass_of($entityClass, ACoreEntity::class)) {
                throw new \TypeError("Class $entityClass must be an instance of " . ACoreEntity::class);
            }
            if (!class_exists($entityClass)) {
                throw new \Error("Class $entityClass not found");
            }

            parent::__construct($entityClass);
        }

        /**
         * Build the array of entities
         * @param array $entityData Array of arrays or objects only
         * @return void
         */
        final public function build (array $entityData): void
        {
            foreach ($entityData as $data) {
                if (!is_object($data) && !is_array($data)) {
                    continue;
                }

                $this->addEntity((new $this->coreEntityClass)->build($data));
            }
        }

        /**
         * Generator for iterating through the core entities
         * @return iterable
         */
        final public function iterateCoreEntities (): iterable
        {
            foreach ($this->iterateEntities() as $entity) {
                yield $entity->getCoreEntity();
            }
        }
    }
