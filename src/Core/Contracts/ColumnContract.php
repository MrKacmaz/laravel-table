<?php

declare(strict_types=1);

namespace LaravelTable\Core\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelTable\Core\Enums\FilterOperator;
use LaravelTable\Core\Enums\SortDirection;
use LaravelTable\Core\Table\Table;

interface ColumnContract
{
    public function boot(): void;

    public function resolve(mixed $row): mixed;

    public function getName(): string;

    public function getCast(): ?string;

    public function setTable(Table $table): void;

    public function isSortable(): bool;

    public function isSearchable(): bool;

    public function isFilterable(): bool;

    public function isVisible(): bool;

    /**
     * @param Builder<Model> $query
     */
    public function applySort(
        Builder $query,
        SortDirection $direction
    ): void;

    /**
     * @param Builder<Model> $query
     */
    public function applySearch(
        Builder $query,
        mixed $value
    ): void;

    /**
     * @param Builder<Model> $query
     */
    public function applyFilter(
        Builder $query,
        FilterOperator $operator,
        mixed $value
    ): void;

    /**
     * @return array{name: string, label: string, cast: string|null, sortable: bool, searchable: bool, filterable: bool, visible: bool}
     */
    public function toArray(): array;

}
