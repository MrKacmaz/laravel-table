<?php

declare(strict_types=1);

namespace LaravelTable\Core\Columns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelTable\Core\Contracts\ColumnContract as ColumnContract;
use LaravelTable\Core\Enums\FilterOperator;
use LaravelTable\Core\Enums\SortDirection;
use LaravelTable\Core\Table\Table;
use RuntimeException;

abstract class BaseColumn implements ColumnContract
{
    protected Table $table;

    protected ?string $label = null;
    protected ?string $castType = null;
    protected bool $sortable = false;
    protected bool $searchable = false;
    protected bool $filterable = false;
    protected bool $formattable = true;

    public function __construct(
        protected string $name,
        protected bool $visible = true
    ) {
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function sortable(bool $state = true): static
    {
        $this->sortable = $state;

        return $this;
    }

    public function searchable(bool $state = true): static
    {
        $this->searchable = $state;

        return $this;
    }

    public function filterable(bool $state = true): static
    {
        $this->filterable = $state;

        return $this;
    }

    public function visible(bool $visible = true): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function formattable(bool $formattable = true): static
    {
        $this->formattable = $formattable;

        return $this;
    }

    public function formatWith(string $formatter): static
    {
        $this->formattable = false;
        $this->castType    = $formatter;

        return $this;
    }

    public function getCast(): ?string
    {
        return $this->castType;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setTable(Table $table): void
    {
        $this->table = $table;
    }

    public function boot(): void
    {
        $this->detectCastFromModel();
    }

    protected function detectCastFromModel(): void
    {
        $casts = $this->table->getModelInstance()->getCasts();

        $column = $this->getName();

        if (! isset($casts[$column])) {
            return;
        }

        $this->castType = $casts[$column];
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function isFilterable(): bool
    {
        return $this->filterable;
    }

    /**
     * @param Builder<Model> $query
     */
    public function applySearch(Builder $query, mixed $value): void
    {
        throw new RuntimeException(
            'applySearch method is not implemented for this column type.'
        );
    }

    /**
     * @param Builder<Model> $query
     */
    public function applySort(Builder $query, SortDirection $direction): void
    {
        throw new RuntimeException(
            'applySort method is not implemented for this column type.'
        );
    }

    /**
     * @param Builder<Model> $query
     */
    public function applyFilter(
        Builder $query,
        FilterOperator $operator,
        mixed $value
    ): void {
        throw new RuntimeException(
            'applyFilter method is not implemented for this column type.'
        );
    }

    /**
     * @return array{name: string, label: string, cast: string|null, sortable: bool, searchable: bool, filterable: bool, visible: bool}
     */
    public function toArray(): array
    {
        return [
            'name'       => $this->getName(),
            'label'      => $this->getLabel() ?? str($this->getName())
                    ->headline()
                    ->toString(),
            'cast'       => $this->getCast(),
            'sortable'   => $this->isSortable(),
            'searchable' => $this->isSearchable(),
            'filterable' => $this->isFilterable(),
            'visible'    => $this->isVisible(),
        ];
    }

    abstract public function resolve(mixed $row): mixed;

}
