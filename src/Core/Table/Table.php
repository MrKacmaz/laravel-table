<?php

declare(strict_types=1);

namespace LaravelTable\Core\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Traits\Macroable;
use LaravelTable\Core\Contracts\ColumnContract;
use LaravelTable\Core\Contracts\QueryEngine;
use LaravelTable\Core\Contracts\TableContract;
use LaravelTable\Core\DTO\TableStateDTO;
use LaravelTable\Core\Query\Pipes\ApplyFilters;
use LaravelTable\Core\Query\Pipes\ApplySearch;
use LaravelTable\Core\Query\Pipes\ApplySorting;
use LaravelTable\Core\Table\Concerns\HasResponse;
use LaravelTable\Core\Table\Concerns\InteractsWithColumns;
use LaravelTable\Core\Table\Concerns\InteractsWithState;
use LaravelTable\Core\Casting\CastManager;
use LaravelTable\Core\Columns\Registry\ColumnRegistry;
use LaravelTable\Core\Serialization\RowSerializer;

abstract class Table implements TableContract
{
    use Macroable;
    use HasResponse;
    use InteractsWithState;
    use InteractsWithColumns;

    protected TableStateDTO $state;
    protected ColumnRegistry $columns;
    protected CastManager $casts;
    protected RowSerializer $serializer;

    /**
     * Get the query builder for the table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract protected function query(): Builder;

    /**
     * Get the table columns.
     *
     * @return array<int, ColumnContract>
     */
    abstract protected function defineColumns(): array;

    public static function make(): static
    {
        return \LaravelTable\Laravel\Facades\TableFactory::make(static::class);
    }

    public function boot(): void
    {
        if (isset($this->serializer)) {
            return;
        }

        $this->columns = new ColumnRegistry($this);
        $this->columns->boot();

        $this->casts = app(CastManager::class);

        $this->serializer = new RowSerializer($this->columns, $this->casts);
    }

    public function getBuilder(): Builder
    {
        $builder = $this->query();

        $this->beforeQuery($builder);

        return app(QueryEngine::class)->apply($builder, $this);
    }

    public function pipes(): array
    {
        return config('laravel-table.pipes', [
            ApplyFilters::class,
            ApplySorting::class,
            ApplySearch::class,
        ]);
    }

    public function paginate(): LengthAwarePaginator
    {
        $paginator = $this->getBuilder()->paginate(
            perPage: $this->state->perPage,
            page: $this->state->page
        );

        $this->afterQuery($paginator);

        return $paginator;
    }

    public function getModelInstance(): Model
    {
        return $this->query()->getModel();
    }

    protected function beforeQuery(Builder $builder): void
    {
        //
    }

    protected function afterQuery(LengthAwarePaginator $paginator): void
    {
        //
    }
}
