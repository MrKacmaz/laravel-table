<?php

declare(strict_types=1);

namespace LaravelTable\Core\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\Enums\FilterOperator;
use LaravelTable\Core\Enums\SortDirection;

final class RelationColumn extends BaseColumn
{
    private function __construct(
        protected string $relation,
        protected string $field,
        protected bool $visible
    ) {
        parent::__construct(
            name: "$relation.$field",
            visible: $visible
        );
    }

    public static function make(
        string $relation,
        string $field,
        bool $visible = true
    ): self {
        return new self(
            relation: $relation,
            field: $field,
            visible: $visible
        );
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function resolve(mixed $row): mixed
    {
        return data_get($row, "{$this->relation}.{$this->field}");
    }

    /**
     * @param Builder<Model> $query
     */
    public function applySort(Builder $query, SortDirection $direction): void
    {
        if (! $this->isSortable()) {
            return;
        }

        $aggregateAlias = str_replace('.', '_', $this->relation) . "_{$this->field}";

        $query->withAggregate($this->relation, $this->field)
            ->orderBy($aggregateAlias, $direction->value);
    }

    /**
     * @param Builder<Model> $query
     */
    public function applySearch(Builder $query, mixed $value): void
    {
        if (! $this->isSearchable()) {
            return;
        }

        $query->whereHas($this->relation, function (Builder $q) use ($value): void {
            $q->where($this->field, 'like', "%{$value}%");
        });
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
            FilterOperator::IN => $query->whereHas(
                $this->relation,
                function (Builder $query) use ($value): void {
                    $query->whereIn(
                        $this->field,
                        (array)$value
                    );
                }
            ),
            FilterOperator::NOT_IN => $query->whereHas(
                $this->relation,
                function (Builder $query) use ($value): void {
                    $query->whereNotIn(
                        $this->field,
                        (array)$value
                    );
                }
            ),
            FilterOperator::BETWEEN => $query->whereHas(
                $this->relation,
                function (Builder $query) use ($value): void {
                    $query->whereBetween(
                        $this->field,
                        (array)$value
                    );
                }
            ),
            default => $query->whereHas(
                $this->relation,
                function (Builder $query) use ($operator, $value): void {
                    $query->where(
                        $this->field,
                        $operator->value,
                        $value
                    );
                }
            )
        };
    }

}
