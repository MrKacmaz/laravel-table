<?php

namespace LaravelTable\Core\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use LaravelTable\Core\DTO\TableStateDTO;

interface TableContract
{
    public function getState(): TableStateDTO;

    public function columns(): array;

    public function getColumn(string $columnName): ?ColumnContract;

    public function paginate(): LengthAwarePaginator;

    public function response(): array;

}
