<?php

declare(strict_types=1);

namespace LaravelTable\Core\Casting\Casters;

use LaravelTable\Core\Contracts\ColumnContract;
use LaravelTable\Core\Contracts\ValueCaster;

class FloatCaster implements ValueCaster
{
    public function cast(mixed $value, ColumnContract $column, mixed $row): ?float
    {
        return $value === null ? null : (float)$value;
    }

}
