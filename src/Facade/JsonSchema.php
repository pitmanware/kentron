<?php
declare(strict_types=1);

namespace Kentron\Facade;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use JsonSchema\Constraints\Factory;
use JsonSchema\Constraints\Constraint;

use Kentron\Template\Alert\TError;

/**
 * Used to validate json against a schema.
 */
class JsonSchema
{
    use TError;

    /**
     * The validation function.
     *
     * @param array|object $jsonData JSON data to be validated.
     * @param object $jsonSchema JSON schema.
     *
     * @return bool The success of the validation.
     */
    public function isValid(array|object $jsonData, object $jsonSchema): bool
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
     * Formats all errors from the json validator.
     *
     * @param string[] $jsonErrors The error array from the json validator.
     *
     * @return void
     */
    private function formatErrors(array $jsonErrors): void
    {
        foreach ($jsonErrors as $jsonError) {
            $this->errors[] = "{$jsonError["pointer"]} - {$jsonError["message"]}";
        }
    }
}
