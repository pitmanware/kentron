<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Controller;

use Kentron\Facade\JsonSchema;
use Kentron\Support\Json;

/**
 * Abstract extension of the base controller for API routes
 */
abstract class AApiController extends AController
{
    final protected function validateBody(string $schema): array|object|null
    {
        $jsonSchema = new JsonSchema();

        $body = $this->transportEntity->getRequestBodyContent();
        $body = Json::isObject($body) ? Json::toObject($body) : Json::toArray($body);

        if (is_null($body)) {
            $this->transportEntity->addError("Request body is not valid JSON. " . Json::handleError());
            $this->transportEntity->setBadRequest();

            return null;
        }

        if (!$jsonSchema->isValid($body, Json::toObject($schema))) {
            $this->transportEntity->addError($jsonSchema->errors);
            $this->transportEntity->setBadRequest();

            return null;
        }

        return $body;
    }
}
