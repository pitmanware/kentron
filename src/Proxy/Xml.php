<?php

    namespace Kentron\Proxy;

    use Utils\Exception\XmlFormatException;

    final class Xml
    {
        public static function extract (string $xml): object
        {
            libxml_use_internal_errors(true);
            $loadedXml = simplexml_load_string($xml);

            if (empty($loadedXml)) {
                throw self::buildException();
            }

            // Format the encoded XML response to an object
            return json_decode(json_encode($loadedXml));
        }

        public static function extractSoap (string $soap): object
        {
            $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soap);

            return self::extract($xml);
        }

        /**
         * Create a nested XmlFormatException
         */
        private static function buildException (): XmlFormatException
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

                if (!empty($error->file)) {
                    $errorMessage .= " in file $error->file";
                }

                // Third parameter is the previous exception
                if (isset($xmlFormatException)) {
                    $xmlFormatException = new XmlFormatException($errorMessage, $error->code, $xmlFormatException);
                }
                else {
                    $xmlFormatException = new XmlFormatException($errorMessage, $error->code);
                }

            }

            return $xmlFormatException;
        }
    }
