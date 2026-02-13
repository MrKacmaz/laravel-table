<?php

declare(strict_types=1);

namespace LaravelTable\Core\Support;

final class Arrays
{
    /**
     * @param array<mixed> $value
     */
    public static function isAssoc(array $value): bool
    {
        return array_keys($value) !== range(0, count($value) - 1);
    }

    /**
     * @return array<mixed>
     */
    public static function wrap(mixed $value): array
    {
        return is_array($value) ? $value : [$value];
    }
}
