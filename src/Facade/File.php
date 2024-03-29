<?php
declare(strict_types=1);

namespace Kentron\Facade;

use \SplFileObject;

final class File extends SplFileObject
{
    public static function open(string $filePath, string $flag): self
    {
        return new self($filePath, $flag);
    }
}
