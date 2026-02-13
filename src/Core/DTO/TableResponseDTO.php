<?php

declare(strict_types=1);

namespace LaravelTable\Core\DTO;

readonly class TableResponseDTO
{
    /**
     * @param array<int, array<string, mixed>> $data
     * @param array<string, int> $meta
     * @param array<int, array<string, mixed>> $columns
     * @param array{prev: string|null, next: string|null} $links
     */
    public function __construct(
        public array $data,
        public array $meta,
        public array $columns,
        public array $links,
    ) {
    }

    /**
     * @return array{data: array<int, array<string, mixed>>, meta: array<string, int>, columns: array<int, array<string, mixed>>, links: array{prev: string|null, next: string|null}}
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'meta' => $this->meta,
            'columns' => $this->columns,
            'links' => $this->links,
        ];
    }

}
