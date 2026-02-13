<?php

declare(strict_types=1);

namespace LaravelTable\Core\Query\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\Enums\FilterOperator;
use LaravelTable\Core\Table\Table;

class ApplyFilters
{
    public function __construct(protected Table $table)
    {
    }

    /**
     * @param   \Illuminate\Database\Eloquent\Builder  $query
     * @param   \Closure                               $next
     *
     * @return mixed
     */
    public function handle(Builder $query, Closure $next): mixed
    {
        foreach (
            $this->table->getState()->filters as $columnName => $operations
        ) {
            $column = $this->table->getColumn($columnName);

            if (! $column || ! $column->isFilterable()) {
                continue;
            }

            foreach ($operations as $operator => $value) {
                $column->applyFilter(
                    query: $query,
                    operator: FilterOperator::from($operator),
                    value: $value
                );
            }
        }

        return $next($query);
    }

}
