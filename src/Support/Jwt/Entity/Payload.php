<?php
declare(strict_types=1);

namespace Kentron\Support\Jwt\Entity;

use Kentron\Support\Json;
use Kentron\Template\Entity\ACoreEntity;

use \DomainException;
use \UnexpectedValueException;

final class Payload extends ACoreEntity
{
    public string $iss;
    public string $sub;
    public string $aud;
    public int $iat;
    public int $exp;

    protected array $propertyMap = [
        "iss" => "iss",
        "sub" => "sub",
        "aud" => "aud",
        "iat" => "iat",
        "exp" => "exp"
    ];

    public function fromString(?string $payload): self
    {
        $payloadObject = Json::toObject($payload);
        if (is_null($payloadObject)) {
            throw new UnexpectedValueException("Payload could not be json decoded");
        }

        $this->hydrate($payloadObject);
        return $this;
    }

    /**
     * Encode a PHP object into a JSON string.
     *
     * @return string JSON representation of the PHP object or array
     *
     * @throws DomainException Provided object could not be encoded to valid JSON
     */
    public function toString(): string
    {
        $json = json_encode(
            $this->normalise(false),
            JSON_UNESCAPED_SLASHES
        );

        if ($error = Json::handleError()) {
            throw new DomainException($error);
        }

        return $json;
    }
}
