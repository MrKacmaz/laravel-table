<?php

declare(strict_types=1);

namespace LaravelTable\Core\Contracts;

interface CapabilityGate
{
    public function allows(string $ability, mixed $arguments = []): bool;

}
