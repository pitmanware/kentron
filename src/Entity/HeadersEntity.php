<?php
declare(strict_types=1);

namespace Kentron\Entity;

use Kentron\Struct\SContentType;

use Kentron\Template\Entity\ACoreEntity;

class HeadersEntity extends ACoreEntity
{
    public string $contentType = SContentType::TYPE_JSON;
    public string $cacheControl = "max-age=300, must-revalidate";
    public string|null $location = null;

    protected array $propertyMap = [
        "Content-Type" => "contentType",
        "Cache-Control" => "cacheControl",
        "Location" => "location"
    ];
}
