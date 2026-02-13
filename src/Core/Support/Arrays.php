<?php

declare(strict_types=1);

namespace LaravelTable\Core\Support;

final class Arrays
{
    public static function isAssoc(array $value): bool
    {
        return array_keys($value) !== range(0, count($value) - 1);
    }

    public static function wrap(mixed $value): array
    {
        return is_array($value) ? $value : [$value];
    }
}
