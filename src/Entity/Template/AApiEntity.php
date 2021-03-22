<?php

namespace Kentron\Entity\Template;

use Kentron\Entity\Template\ACoreEntity;

use Kentron\Facade\JsonSchema;

abstract class AApiEntity extends ACoreEntity
{
    /**
     * The full content of the JSON schema
     *
     * @var string|null
     */
    private $schema;

    /**
     * Validates the given JSON string against the schema for this entity
     *
     * @param string $json The request JSON
     *
     * @return object|null The extracted JSON or null if failed
     */
    public function validate(string $json): ?object
    {
        if (!isset($this->schema)) {
            $this->addError("JSON schema not set");
            return null;
        }

        $jsonService = new JsonSchema();

        $json = $jsonService->extract($json);
        $schema = $jsonService->extract($this->schema);

        $jsonService->isValid($json, $schema);

        $this->addError($jsonService->getErrors());

        return $json;
    }

    /**
     * Sets the JSON schema for validation
     *
     * @param string $schema The content of the JSON schema
     */
    final public function setSchema(string $schema): void
    {
        $this->schema = $schema;
    }
}
