<?php
declare(strict_types=1);

namespace Kentron\Template\Store\Provider;

interface IProviderEntity
{
    public function getId(): int;
    public function getDisplayName(): string;
    public function getConstantName(): string;
}
