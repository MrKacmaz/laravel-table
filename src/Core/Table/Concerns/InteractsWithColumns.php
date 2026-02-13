<?php

declare(strict_types=1);

namespace LaravelTable\Core\Table\Concerns;

use LaravelTable\Core\Contracts\ColumnContract;

trait InteractsWithColumns
{
    /**
     * @return array<int, ColumnContract>
     */
    public function columns(): array
    {
        return $this->columns->all();
    }

    public function getColumn(string $columnName): ?ColumnContract
    {
        return $this->columns->get($columnName);
    }

}
