<?php

declare(strict_types=1);

namespace LaravelTable\Engine;

use LaravelTable\Core\Contracts\ValueCaster;
use LaravelTable\Core\Contracts\ColumnContract;

class CastManager
{
    /**
     * @param array<string, class-string<ValueCaster>> $casters
     */
    public function __construct(protected array $casters)
    {
    }

    public function cast(
        ?string $type,
        mixed $value,
        ColumnContract $column,
        mixed $row
    ): mixed {
        if (! $type) {
            return $value;
        }

        $normalizedType = strtolower(strtok($type, ':') ?: $type);
        $casterClass = $this->casters[$normalizedType] ?? null;

        if (! $casterClass) {
            return $value;
        }

        return app($casterClass)->cast($value, $column, $row);
    }

}
