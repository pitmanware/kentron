<?php
declare(strict_types=1);

namespace Kentron\Support;

use Kentron\Exception\XmlFormatException;

use Kentron\Facade\View;
use \SimpleXMLElement;

final class Xml
{
    /**
     * Builds the XML string
     *
     * @param string $viewPath The path to the twig file
     * @param string $action   The name of the twig file to populate
     * @param array  $data     The data to populate the twig file
     *
     * @return string|null Null if the data is not in the correct format
     */
    public static function build(string $viewPath, string $action, array $data): ?string
    {
        return (new View(dirname($viewPath), basename($viewPath), $action))->capture($data);
    }

    /**
     * Extract the raw XML
     *
     * @param string $xml       The XML string to extract
     * @param bool   $allowNull If false, throws exception instead
     *
     * @return array The formatted extracted array
     *
     * @throws XmlFormatException If the XML could not be decoded
     */
    public static function extract(string $xml, bool $allowNull = false): ?array
    {
        libxml_use_internal_errors(true);
        $loadedXml = simplexml_load_string($xml);

        if (!($xml instanceof SimpleXMLElement)) {
            if ($allowNull) {
                return null;
            }

            throw self::buildException();
        }

        return self::format($loadedXml);
    }

    /**
     * Calls self::extract after removing soap attributes
     *
     * @param string $soap The raw XML string
     *
     * @return array
     */
    public static function extractSoap(string $soap): array
    {
        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soap);

        return self::extract($xml);
    }

    /**
     * Format the extracted XML to a more useable array
     *
     * @param SimpleXMLElement $xml The extracted XML to format
     *
     * @return array The formatted extracted XML array
     */
    public static function format(SimpleXMLElement $xml): array
    {
        $collection = [];
        $nodes = $xml->children();
        $attributes = $xml->attributes();

        if (count($attributes) > 0) {
            foreach ($attributes as $attrName => $attrValue) {
                $collection["@attributes"][$attrName] = (string) $attrValue;
            }
        }

        if ($nodes->count() === 0) {
            $collection["value"] = (string) $xml;
            return $collection;
        }

        /** @var SimpleXMLElement $nodeValue */
        foreach ($nodes as $nodeName => $nodeValue) {
            if (count($nodeValue->xpath('../' . $nodeName)) < 2) {
                $collection[$nodeName] = self::format($nodeValue);
                continue;
            }

            $collection[$nodeName][] = self::format($nodeValue);
        }

        return $collection;
    }

    /**
     * Create a nested exception
     *
     * @return XmlFormatException
     */
    private static function buildException(): XmlFormatException
    {
        $errors = (array) libxml_get_errors();

        foreach ($errors as $error) {
            switch ($error->level) {
                case LIBXML_ERR_WARNING:
                    $errorMessage = "Warning: ";
                    break;

                case LIBXML_ERR_ERROR:
                    $errorMessage = "Error: ";
                    break;

                case LIBXML_ERR_FATAL:
                    $errorMessage = "Fatal Error: ";
                    break;
            }

            $errorMessage .= "$error->message at $error->line:$error->column";

            if (isset($error->file)) {
                $errorMessage .= " in file $error->file";
            }

            if (!isset($xmlFormatException)) {
                $xmlFormatException = new XmlFormatException($errorMessage, $error->code);
                continue;
            }

            // Third parameter is the previous exception
            $xmlFormatException = new XmlFormatException($errorMessage, $error->code, $xmlFormatException);

        }

        return $xmlFormatException;
    }
}
