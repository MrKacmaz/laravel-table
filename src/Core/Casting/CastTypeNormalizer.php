<?php

namespace LaravelTable\Core\Casting;

class CastTypeNormalizer
{
    public function normalize(?string $type): ?string
    {
        if ($type === null || $type === '') {
            return null;
        }

        return strtolower(strtok($type, ':') ?: $type);
    }

}
