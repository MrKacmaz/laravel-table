<?php

namespace LaravelTable\Core\Serialization;

use LaravelTable\Core\Contracts\ColumnContract;

class ColumnSerializer
{
    public function serialize(ColumnContract $column): array
    {
        return $column->toArray();
    }

}
