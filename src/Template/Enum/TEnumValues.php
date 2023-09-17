<?php
declare(strict_types=1);

namespace Kentron\Template\Enum;

use \BackedEnum;
use \Error;

trait TEnumValues
{
    /**
     * Get all values of an enum
     *
     * @return string[]
     *
     * @throws Error If this is called on a class that does not implement \BackedEnum
     */
    public static function values(): array
    {
        if (!is_a(self::class, BackedEnum::class, true)) {
            throw new Error("TEnumValues can only be used in backed enums");
        }

        return array_map(
            fn(BackedEnum $method) => $method->value,
            self::cases()
        );
    }
}
