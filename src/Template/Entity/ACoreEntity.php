<?php

    namespace Kentron\Template\Entity;

    use Kentron\Entity\Entity;

    abstract class ACoreEntity extends Entity
    {
        /**
         * Relevant property map to build the entity dynamically
         * Should be overridden
         * @var array
         * @example [
         *     "property_key" => [
         *         "get" => "getClassProperty",
         *         "set" => "setClassProperty",
         *         "get_class" => "getClassInstance" // For nesting; has to be on the extended ADBEntity or AApiEntity
         *         "set_class" => "setClassInstance" // For nesting; has to be on the extended ADBEntity or AApiEntity
         *     ]
         * ]
         */
        protected $propertyMap = [];

        /**
         * The core entity
         * @var Entity
         */
        protected $coreEntity;

        /**
         * Constructor can only be called by child
         * Should be overridden
         * @param Entity $entity Supplies the core entity
         */
        protected function __construct (Entity $entity)
        {
            $this->coreEntity = $entity;
        }

        /**
         * Destroy the core entity on destruct
         */
        public function __destruct ()
        {
            $this->coreEntity = null;
        }

        /**
         * Builds the entity from the database or API
         * @param  array|object $data The input data
         * @return self
         */
        final public function build ($data): self
        {
            if (!is_object($data) && !is_array($data)) {
                return $this;
            }

            foreach ($this->propertyMap as $property => $binding) {

                $dataProperty = $this->getDataProperty($data, $property);

                if (is_null($dataProperty)) {
                    continue;
                }

                $setter = $binding["set"] ?? null;

                if (is_object($dataProperty) || is_array($dataProperty)) {
                    // If the property is an object or array,
                    // check to see if there is a getter for the class
                    $classGetter = $binding["get_class"] ?? null;

                    if ($this->isValidMethod($this, $classGetter)) {
                        $entity = $this->{$classGetter}();

                        // The subclass must be another instance of ADBEntity or AApiEntity
                        if ($this->entityIsRecursive($entity)) {
                            $entity->build($dataProperty);
                        }

                        $classSetter = $binding["set_class"] ?? null;

                        if ($this->isValidMethod($this, $classSetter)) {
                            $this->{$classSetter}($entity);
                        }

                        // Overwrite the property so the setter doesn't need to be duplicated
                        $dataProperty = $entity->getCoreEntity();
                    }
                }

                if ($this->isValidMethod($this->coreEntity, $setter)) {
                    $this->coreEntity->{$setter}($dataProperty);
                }
            }

            return $this;
        }

        /**
         * Dynamically call methods on the core entity
         * @param  string $callable The method to call
         * @param  array  $args     The arguments to pass into the method
         * @return mixed
         * @throws BadMethodCallException On invalid method call
         */
        final public function __call (string $callable, array $args = [])
        {
            return $this->coreEntity->{$callable}(...$args);
        }

        /**
         * Alternatively the core entity can be returned
         * @return Entity
         */
        final public function getCoreEntity (): Entity
        {
            return $this->coreEntity;
        }

        /**
         * Generator for the properties
         * @return iterable
         * @throws \TypeError
         */
        final public function iterateProperties (bool $allowNullable = false): iterable
        {
            foreach ($this->propertyMap as $key => $binding) {
                $getter = $binding["get"] ?? null;

                if (!$this->isValidMethod($this->coreEntity, $getter)) {
                    continue;
                }

                try {
                    $propertyValue = $this->coreEntity->{$getter}();
                }
                catch (\TypeError $te) {
                    if (!$allowNullable) {
                        throw $te;
                    }
                    $propertyValue = null;
                }

                // Don't return entities when iterating properties
                if (is_object($propertyValue) && is_subclass_of($propertyValue, Entity::class)) {
                    continue;
                }

                yield $key => $propertyValue;
            }
        }

        /**
         * Abstract method to get property from the input build data
         * @param array|object $data
         * @param string       $property
         * @return mixed
         */
        abstract protected function getDataProperty ($data, string $property);

        /**
         * Validates a callable method on the core entity
         * @param  string|null $method The method to check
         * @return bool
         */
        final protected function isValidMethod (Entity $entity, ?string $method = null): bool
        {
            if (
                isset($method) &&
                method_exists($entity, $method) &&
                is_callable([$entity, $method])
            ) {
                return true;
            }

            return false;
        }

        /**
         * Allows for recursive building if entity extends this
         * @return bool
         */
        final private function entityIsRecursive (Entity $entity): bool
        {
            if (is_subclass_of($entity, __CLASS__)) {
                return true;
            }

            return false;
        }
    }
