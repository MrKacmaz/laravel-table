<?php

declare(strict_types=1);

namespace LaravelTable\Core\Table\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;

trait HasResponse
{
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

    protected function meta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
        ];
    }

    public function serializeRows(array $rows): array
    {
        return array_map(fn ($row) => $this->serializer->serialize($row), $rows);
    }

    protected function serializeColumns(): array
    {
        return $this->serializer->serializeColumns();
    }

}
