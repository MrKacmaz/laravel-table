<?php

namespace LaravelTable\Core\Query\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\Table\Table;

class ApplySearch
{
    public function __construct(protected Table $table)
    {
    }

    public function handle(Builder $query, Closure $next): mixed
    {
        $state = $this->table->getState();

        if (! $state->search) {
            return $next($query);
        }

        $query->where(function ($q) use ($state) {
            foreach ($this->table->columns() as $column) {
                if (! $column->isSearchable()) {
                    continue;
                }

                $column->applySearch($q, $state->search);
            }
        });

        return $next($query);
    }

}
