<?php

declare(strict_types=1);

namespace LaravelTable\Core\Table\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;

trait HasResponse
{
    /**
     * @return array{data: array<int, array<string, mixed>>, meta: array<string, int>, columns: array<int, array<string, mixed>>, links: array{prev: string|null, next: string|null}}
     */
    public function response(): array
    {
        $paginator = $this->paginate();

        return [
            'data'    => $this->serializeRows($paginator->items()),
            'meta'    => $this->meta($paginator),
            'columns' => $this->serializeColumns(),
            'links'   => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    /**
     * @param LengthAwarePaginator<int, mixed> $paginator
     * @return array<string, int>
     */
    protected function meta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
        ];
    }

    /**
     * @param array<int, mixed> $rows
     * @return array<int, array<string, mixed>>
     */
    public function serializeRows(array $rows): array
    {
        return array_map(fn ($row) => $this->serializer->serialize($row), $rows);
    }

    /**
     * @return array<int, array{name: string, label: string, cast: string|null, sortable: bool, searchable: bool, filterable: bool, visible: bool}>
     */
    protected function serializeColumns(): array
    {
        return $this->serializer->serializeColumns();
    }

}
