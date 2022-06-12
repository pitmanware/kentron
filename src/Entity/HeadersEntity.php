<?php
declare(strict_types=1);

namespace Kentron\Entity;

use Kentron\Entity\Template\ACoreEntity;

class HeadersEntity extends ACoreEntity
{
    /**
     * @var string
     */
    private $contentType;
    /**
     * @var string
     */
    private $cacheControl;
    /**
     * @var string|null
     */
    private $location = null;
    
    protected array $propertyMap = [
        "Content-Type" => [
            "get" => "getContentType",
            "set" => "setContentType"
        ],
        "Cache-Control" => [
            "get" => "getCacheControl",
            "set" => "setCacheControl"
        ],
        "Location" => [
            "get" => "getLocation",
            "set" => "setLocation"
        ]
    ];

    /**
     * Getters
     */

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getCacheControl(): string
    {
        return $this->cacheControl;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * Setters
     */

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function setCacheControl(string $cacheControl): void
    {
        $this->cacheControl = $cacheControl;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }
}
