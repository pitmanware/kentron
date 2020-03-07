<?php

namespace Kentron\Facade;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use JsonSchema\Constraints\Factory;
use JsonSchema\Constraints\Constraint;

/**
 * Used to validate json against a schema.
 */
class JsonSchema
{
    /**
     * Any errors from the validation.
     * @var array
     */
    public $errors = [];

    /**
     * Extract a JSON string
     * @param  string      $json The JSON data to be extracted
     * @return object|null
     */
    public function extract (string $json): ?object
    {
        return json_decode($json, false);
    }

    /**
     * The validation function.
     * @param  object $jsonData   JSON data to be validated.
     * @param  object $jsonSchema JSON schema.
     * @return bool               The success of the validation.
     */
    public function isValid (object $jsonData, object $jsonSchema): bool
    {
        $schemaStorage = new SchemaStorage();

        $schemaStorage->addSchema("file://mySchema", $jsonSchema);

        $jsonValidator = new Validator( new Factory($schemaStorage) );

        $jsonValidator->validate($jsonData, $jsonSchema, Constraint::CHECK_MODE_APPLY_DEFAULTS);

        $jsonErrors = $jsonValidator->getErrors();

        if (count($jsonErrors) > 0) {
            $this->formatErrors($jsonErrors);
            return false;
        }

        return true;
    }

    /**
     * Gets any errors
     *
     * @return array
     */
    public function getErrors (): array
    {
        return $this->errors;
    }

    /**
     * Formats all errors from the json validator.
     * @param  array  $jsonErrors The error array from the json validator.
     * @return void
     */
    private function formatErrors (array $jsonErrors): void
    {
        foreach ($jsonErrors as $jsonError) {
            $this->errors[] = "{$jsonError["pointer"]} - {$jsonError["message"]}";
        }
    }
}
