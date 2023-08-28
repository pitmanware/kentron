<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Controller;

use Kentron\Enum\EStatusCode;
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

        $body = Json::extract($this->transportEntity->getRequestBodyContent());

        if (is_null($body)) {
            $this->transportEntity->addError("Request body is not valid JSON. " . Json::handleError());
            $this->transportEntity->setStatusCode(EStatusCode::CODE_400);

            return null;
        }

        if (!$jsonSchema->isValid($body, Json::toObject($schema))) {
            $this->transportEntity->addError($jsonSchema->errors);
            $this->transportEntity->setStatusCode(EStatusCode::CODE_422);

            return null;
        }

        return $body;
    }
}
