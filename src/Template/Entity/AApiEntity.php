<?php
declare(strict_types=1);

namespace Kentron\Template\Entity;

use Kentron\Template\Entity\ACoreEntity;

use Kentron\Facade\JsonSchema;
use Kentron\Support\Json;

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

        $json = Json::extract($json);

        if (is_null($json)) {
            $this->addError("JSON data is not valid");
            return null;
        }

        $schema = Json::toObject($this->schema);

        if (is_null($json)) {
            $this->addError("JSON schema is not valid");
            return null;
        }

        $jsonService = new JsonSchema();
        $isValid = $jsonService->isValid($json, $schema);

        $this->mergeErrors($jsonService);

        return $isValid ? $json : null;
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
