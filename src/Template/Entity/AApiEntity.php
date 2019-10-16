<?php

    namespace Kentron\Template\Entity;

    use Kentron\Template\Entity\ACoreEntity;

    use Kentron\Facade\JsonSchema;

    abstract class AApiEntity extends ACoreEntity
    {
        /**
         * The file path of the schema
         * @var string|null
         * @example /var/www/html/project/App/Schema/validation.schema.json
         */
        private $schema;

        /**
         * Validates the given JSON string against the schema for this entity
         * @param  string      $json The request JSON
         * @return object|null       The extracted JSON or null if failed
         */
        public function validate (string $json): ?object
        {
            if (!isset($this->schema)) {
                $this->addError("JSON schema not set");
                return null;
            }
            if (!@file_exists($this->schema)) {
                $this->addError("JSON schema file does not exist");
                return null;
            }
            if (!@is_readable($this->schema)) {
                $this->addError("JSON schema file cannot be read");
                return null;
            }

            $jsonService = new JsonSchema();

            $jsonEntity = $jsonService->validate(file_get_contents($this->schema), $json);

            if ($jsonEntity->hasErrors()) {
                $this->addError($jsonEntity->getErrors());
                return null;
            }

            return $jsonEntity->extracted;
        }

        /**
         * Sets the JSON schema for validation
         * @param string $schema The absolute path to the schema file
         */
        final public function setSchema (string $schema): void
        {
            $this->schema = $schema;
        }

        /**
         * Overridden method to get the input data property if it exists
         * @param  object $data     The data from the API
         * @param  string $property An expected key
         * @return mixed            Null if doesn't exist
         */
        final protected function getDataProperty ($data, string $property)
        {
            if (is_object($data) && property_exists($data, $property)) {
                return $data->{$property};
            }

            return null;
        }
    }
