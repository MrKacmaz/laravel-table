<?php

declare(strict_types=1);

namespace LaravelTable\Core\Enums;

use LaravelTable\Core\Support\EnumUtilities;

enum SortDirection: string
{
    use EnumUtilities;

    case ASC = 'asc';
    case DESC = 'desc';

    public static function fromRequest(?string $value): self
    {
        return match (strtolower($value ?? '')) {
            'desc' => self::DESC,
            default => self::ASC,
        };
    }

    public function isAsc(): bool
    {
        return $this === self::ASC;
    }

}
