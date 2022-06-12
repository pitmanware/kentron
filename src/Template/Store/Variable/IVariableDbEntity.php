<?php
declare(strict_types=1);

namespace Kentron\Template\Store\Variable;

interface IVariableDbEntity
{
    public function getDisplayName(): string;
    public function getConstantName(): string;
    public function getValue(): mixed;
    public function getType(): string;
    public function isEncrypted(): bool;
    public function getDescription(): ?string;
}
