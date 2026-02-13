<?php

declare(strict_types=1);

namespace LaravelTable\Core\DTO;

use LaravelTable\Core\Enums\SortDirection;

readonly class TableStateDTO
{
    /**
     * @param array<string, array<string, mixed>> $filters
     */
    public function __construct(
        public string|null $sort,
        public SortDirection $direction,
        public array $filters,
        public string|null $search,
        public int $page,
        public int $perPage,
    ) {
    }

}
