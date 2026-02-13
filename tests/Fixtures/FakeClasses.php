<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use LaravelTable\Core\Support\EnumUtilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelTable\Core\Columns\BaseColumn;
use LaravelTable\Core\Contracts\ColumnContract;
use LaravelTable\Core\Table\Table;

final class FakeModel extends Model
{
    use HasFactory;
    protected $table = 'fake_models';

    protected $casts = [
        'age' => 'integer',
        'is_active' => 'boolean',
    ];
}

final class DummyColumn extends BaseColumn
{
    public function resolve(mixed $row): mixed
    {
        return data_get($row, $this->name);
    }
}

final class FakeTable extends Table
{
    private Builder $builder;

    /** @var array<int, ColumnContract> */
    private array $definedColumns = [];

    public bool $beforeCalled = false;
    public bool $afterCalled = false;

    public function setBuilder(Builder $builder): self
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @param array<int, ColumnContract> $columns
     */
    public function setDefinedColumns(array $columns): self
    {
        $this->definedColumns = $columns;

        return $this;
    }

    protected function query(): Builder
    {
        return $this->builder;
    }

    protected function defineColumns(): array
    {
        return $this->definedColumns;
    }

    protected function beforeQuery(Builder $builder): void
    {
        $this->beforeCalled = true;
    }

    protected function afterQuery(LengthAwarePaginator $paginator): void
    {
        $this->afterCalled = true;
    }
}

final class EnumUtilitiesProbe
{
    use EnumUtilities;

    public function __construct(public string $value)
    {
    }
}
