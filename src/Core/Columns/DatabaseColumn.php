<?php

declare(strict_types=1);

namespace LaravelTable\Core\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\Enums\FilterOperator;
use LaravelTable\Core\Enums\SortDirection;

final class DatabaseColumn extends BaseColumn
{
    private function __construct(string $name, bool $visible)
    {
        parent::__construct(
            name: $name,
            visible: $visible
        );
    }

    public static function make(string $name, bool $visible = true): self
    {
        return new self(
            name: $name,
            visible: $visible
        );
    }

    public function resolve(mixed $row): mixed
    {
        return data_get($row, $this->name);
    }

    /**
     * @param Builder<Model> $query
     */
    public function applySort(Builder $query, SortDirection $direction): void
    {
        if (! $this->isSortable()) {
            return;
        }

        $query->orderBy($this->name, $direction->value);
    }

    /**
     * @param Builder<Model> $query
     */
    public function applySearch(Builder $query, mixed $value): void
    {
        if (! $this->isSearchable()) {
            return;
        }

        $query->where($this->name, 'like', "%{$value}%");
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

        match ($operator) {
            FilterOperator::IN => $query->whereIn(
                $this->name,
                (array)$value
            ),
            FilterOperator::NOT_IN => $query->whereNotIn(
                $this->name,
                (array)$value
            ),
            FilterOperator::BETWEEN => $query->whereBetween(
                $this->name,
                (array) $value
            ),
            default => $query->where($this->name, $operator->value, $value),
        };
    }

}
