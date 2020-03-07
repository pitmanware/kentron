<?php

namespace Kentron\Service;

final class Schema
{
    /**
     * Gets a schema with all the associated definition files
     * @param  string $schemaDir  The path to the schemas
     * @param  string $actionPath The path to the route relevant schemas
     * @param  string $schemaName The name of the route relevant schema
     * @return string
     */
    public static function get (string $schemaDir, string $actionPath, string $schemaName): string
    {
        $baseDefinitionsPath = "$schemaDir/Definitions.schema.json";
        $actionDefinitionsPath = "$schemaDir/$actionPath/Definitions.schema.json";
        $schemaPath = "$schemaDir/$actionPath/$schemaName.schema.json";

        self::validateFiles($baseDefinitionsPath, $actionDefinitionsPath, $schemaPath);

        $schema = array_replace_recursive(
            self::getFileContent($baseDefinitionsPath) ?? [],
            self::getFileContent($actionDefinitionsPath) ?? [],
            self::getFileContent($schemaPath) ?? []
        );

        return json_encode($schema);
    }

    /**
     * Validates a file path
     * @param array $files A collection of file paths
     * @return void
     * @throws \ErrorException If the file does not exist or is not readable
     * @throws \ErrorException If the file content is not valid JSON
     */
    private static function validateFiles (string &...$files): void
    {
        foreach ($files as &$file) {
            if (!$filePath = realpath($file)) {
                throw new \ErrorException("File $file does not exist or is unreadable");
            }

            if (is_null(self::getFileContent($filePath))) {
                throw new \ErrorException("File $file does not contain valid JSON");
            }

            $file = $filePath;
        }
    }

    /**
     * Gets the JSON decoded content of a file
     * @param string $filePath The path to the JSON file
     * @return array|null Null if the JSON is invalid
     */
    private static function getFileContent (string $filePath): ?array
    {
        return json_decode(@file_get_contents($filePath), true);
    }
}
