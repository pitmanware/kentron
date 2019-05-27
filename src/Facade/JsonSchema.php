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
         * @param  string     $json The json data to be extracted
         * @return object
         * @throws \Exception If the string cannot be successfully decoded
         */
        public function extract (string $json): object
        {
            $extracted = json_decode($json, false);
            
            if (is_null()) {
                throw new \Excpetion("JSON string is invalid");
            }
        }

        /**
         * The validation function.
         * @param  string $jsonData   JSON encoded data to be validated.
         * @param  string $jsonSchema JSON encoded schema.
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
