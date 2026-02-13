<?php

declare(strict_types=1);

namespace LaravelTable\Core\Serialization;

use LaravelTable\Core\Contracts\ColumnContract;

class ColumnSerializer
{
    /**
     * @return array{name: string, label: string, cast: string|null, sortable: bool, searchable: bool, filterable: bool, visible: bool}
     */
    public function serialize(ColumnContract $column): array
    {
        return $column->toArray();
    }

}
