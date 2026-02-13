<?php

declare(strict_types=1);

namespace LaravelTable\Core\Columns;

use Illuminate\Database\Eloquent\Model;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\Enums\FilterOperator;
use LaravelTable\Core\Enums\SortDirection;
use RuntimeException;

final class ComputedColumn extends BaseColumn
{
    private function __construct(
        string $name,
        protected Closure $resolver,
        bool $visible,
    ) {
        parent::__construct(
            name: $name,
            visible: $visible
        );
    }

    public static function make(
        string $name,
        Closure $resolver,
        bool $visible = true,
    ): self {
        return new self(
            name: $name,
            resolver: $resolver,
            visible: $visible
        );
    }

    public function resolve(mixed $row): mixed
    {
        return call_user_func($this->resolver, $row);
    }

    /**
     * @param Builder<Model> $query
     */
    public function applySort(Builder $query, SortDirection $direction): void
    {
        if (! $this->isSortable()) {
            return;
        }

        throw new RuntimeException('Computed columns cannot be sorted.');
    }

    /**
     * @param Builder<Model> $query
     */
    public function applySearch(Builder $query, mixed $value): void
    {
        if (! $this->isSearchable()) {
            return;
        }

        throw new RuntimeException('Computed columns cannot be searched.');
    }

    /**
     * @param Builder<Model> $query
     */
    public function applyFilter(
        Builder $query,
        FilterOperator $operator,
        mixed $value
    ): void {
        if (! $this->isFilterable()) {
            return;
        }

        throw new RuntimeException('Computed columns cannot be filtered.');
    }

}
