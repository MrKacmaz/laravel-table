<?php

declare(strict_types=1);

namespace LaravelTable\Core\Casting\Casters;

use LaravelTable\Core\Contracts\ColumnContract;
use LaravelTable\Core\Contracts\ValueCaster;

class IntCaster implements ValueCaster
{
    public function cast(mixed $value, ColumnContract $column, mixed $row): ?int
    {
        return $value === null ? null : (int)$value;
    }

}
