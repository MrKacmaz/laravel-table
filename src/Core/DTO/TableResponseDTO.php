<?php

namespace LaravelTable\Core\DTO;

readonly class TableResponseDTO
{
    public function __construct(
        public array $data,
        public array $meta,
        public array $columns,
        public array $links,
    ) {
    }

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
