<?php
declare(strict_types=1);

namespace Kentron\Entity;

use Kentron\Enum\EContentType;

use Kentron\Template\Entity\ACoreEntity;

class HeadersEntity extends ACoreEntity
{
    public EContentType $contentType = EContentType::TYPE_JSON;
    public string $cacheControl = "max-age=300, must-revalidate";
    public string|null $location = null;

    protected array $propertyMap = [
        "Content-Type" => [
            "get" => ["contentType", "value"]
        ],
        "Cache-Control" => "cacheControl",
        "Location" => "location"
    ];
}
