<?php

namespace LaravelTable\Core\Table\Concerns;

use LaravelTable\Core\Contracts\ColumnContract;

trait InteractsWithColumns
{
    public function columns(): array
    {
        return $this->columns->all();
    }

    public function getColumn(string $columnName): ?ColumnContract
    {
        return $this->columns->get($columnName);
    }

}
