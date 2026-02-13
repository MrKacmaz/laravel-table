<?php

declare(strict_types=1);

namespace LaravelTable\Core\Query\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelTable\Core\Table\Table;

class ApplySorting
{
    public function __construct(protected Table $table)
    {
    }

    /**
     * @param Builder<Model> $query
     */
    public function handle(Builder $query, Closure $next): mixed
    {
        $state = $this->table->getState();

        if (! $state->sort) {
            return $next($query);
        }

        $column = $this->table->getColumn($state->sort);

        if ($column && $column->isSortable()) {
            $column->applySort(
                query: $query,
                direction: $state->direction
            );
        }

        return $next($query);
    }

}
