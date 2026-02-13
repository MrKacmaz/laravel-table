<?php

declare(strict_types=1);

namespace LaravelTable\Engine;

use LaravelTable\Core\Contracts\ColumnContract;

class RowSerializer
{
    public function __construct(
        protected ColumnRegistry $columns,
        protected CastManager $casts,
    ) {
    }

    /**
     * @return array<int, array{name: string, label: string, cast: string|null, sortable: bool, searchable: bool, filterable: bool, visible: bool}>
     */
    public function serializeColumns(): array
    {
        return array_values(
            array_map(
                fn (ColumnContract $c): array => $c->toArray(),
                array_filter(
                    $this->columns->all(),
                    fn (ColumnContract $c): bool => $c->isVisible()
                )
            )
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function serialize(mixed $row): array
    {
        $data = [];

        foreach ($this->columns->all() as $column) {
            /** @var ColumnContract $column */
            if (! $column->isVisible()) {
                continue;
            }

            $data[$column->getName()] = $this->transformValue($column, $row);
        }

        return $data;
    }

    protected function transformValue(ColumnContract $column, mixed $row): mixed
    {
        $value = $column->resolve($row);

        return $this->casts->cast(
            $column->getCast(),
            $value,
            $column,
            $row
        );
    }

}
