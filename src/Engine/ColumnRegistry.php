<?php

declare(strict_types=1);

namespace LaravelTable\Engine;

use LaravelTable\Core\Contracts\ColumnContract;
use LaravelTable\Core\Table\Table;

class ColumnRegistry
{
    public function __construct(protected Table $table)
    {
    }

    protected array $columns = [];
    protected array $columnMap = [];

    public function boot(): void
    {
        if (! empty($this->columns)) {
            return;
        }

        $this->columns = $this->table->defineColumns();

        foreach ($this->columns as $column) {
            $column->setTable($this->table);
            $column->boot();
            $this->columnMap[$column->getName()] = $column;
        }
    }

    public function all(): array
    {
        return $this->columns;
    }

    public function get(string $name): ?ColumnContract
    {
        return $this->columnMap[$name] ?? null;
    }

}
