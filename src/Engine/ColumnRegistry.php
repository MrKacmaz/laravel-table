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

    /** @var array<int, ColumnContract> */
    protected array $columns = [];
    /** @var array<string, ColumnContract> */
    protected array $columnMap = [];

    public function boot(): void
    {
        if (! empty($this->columns)) {
            return;
        }

        $this->columns = $this->table->getDefinedColumns();

        foreach ($this->columns as $column) {
            $column->setTable($this->table);
            $column->boot();
            $this->columnMap[$column->getName()] = $column;
        }
    }

    /**
     * @return array<int, ColumnContract>
     */
    public function all(): array
    {
        return $this->columns;
    }

    public function get(string $name): ?ColumnContract
    {
        return $this->columnMap[$name] ?? null;
    }

}
