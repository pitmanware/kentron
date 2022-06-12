<?php
declare(strict_types=1);

namespace Kentron\Support\Jwt\Entity;

use Kentron\Support\Json;
use Kentron\Template\Entity\ACoreEntity;

use \DomainException;
use \UnexpectedValueException;

final class Header extends ACoreEntity
{
    public string $typ = "JWT";
    public string $alg = "";
    public mixed $kid = null;

    protected array $propertyMap = [
        "typ" => "typ",
        "alg" => "alg",
        "kid" => "kid"
    ];

    public function fromString(?string $header): self
    {
        $headerObject = Json::toObject($header);
        if (is_null($headerObject)) {
            throw new UnexpectedValueException("Header could not be json decoded");
        }

        $this->hydrate($headerObject);
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

    public function verify(): void
    {
        if (!isset($this->alg)) {
            throw new UnexpectedValueException('Empty algorithm');
        }
        Algorithm::exists($this->alg);
    }
}
