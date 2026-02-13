<?php

declare(strict_types=1);

namespace LaravelTable\Core\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use LaravelTable\Core\DTO\TableStateDTO;

interface TableContract
{
    public function getState(): TableStateDTO;

    /**
     * @return array<int, ColumnContract>
     */
    public function columns(): array;

    public function getColumn(string $columnName): ?ColumnContract;

    /**
     * @return LengthAwarePaginator<int, mixed>
     */
    public function paginate(): LengthAwarePaginator;

    /**
     * @return array{data: array<int, array<string, mixed>>, meta: array<string, int>, columns: array<int, array<string, mixed>>, links: array{prev: string|null, next: string|null}}
     */
    public function response(): array;

}
