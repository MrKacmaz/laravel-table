<?php

declare(strict_types=1);

namespace LaravelTable\Core\Query\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelTable\Core\Table\Table;

class ApplySearch
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

        if (! $state->search) {
            return $next($query);
        }

        $query->where(function (Builder $q) use ($state): void {
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
