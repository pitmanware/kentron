<?php
declare(strict_types=1);

namespace Kentron\Template\Store;

use Kentron\Support\Jwt\Entity\Payload;
use Kentron\Support\Jwt\Entity\Header;

interface IJwtStore
{
    /**
     * Return the JWT Payload entity
     *
     * @return Payload
     */
    public static function getJwtPayload(): Payload;

    /**
     * Return the JWT Header entity
     *
     * @return Header
     */
    public static function getJwtHeader(): Header;

    /**
     * Get the auth key
     *
     * @return string
     *
     * @throws \Error If the key is not set
     */
    public static function getJwtAuthKey(): string;
}
