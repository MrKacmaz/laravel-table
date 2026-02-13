<?php

declare(strict_types=1);

namespace LaravelTable\Core\Contracts;

interface ValueCaster
{
    public function cast(
        mixed $value,
        ColumnContract $column,
        mixed $row
    ): mixed;

}
