<?php

namespace LaravelTable\Core\Contracts;

interface CapabilityGate
{
    public function allows(string $ability, mixed $arguments = []): bool;

}
