<?php

declare(strict_types=1);

namespace LaravelTable\Laravel\Capabilities;

use Illuminate\Support\Facades\Gate;
use LaravelTable\Core\Contracts\CapabilityGate;

class LaravelCapabilityGate implements CapabilityGate
{
    public function allows(string $ability, mixed $arguments = []): bool
    {
        return Gate::allows($ability, $arguments);
    }

}
