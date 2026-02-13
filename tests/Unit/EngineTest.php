<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use LaravelTable\Core\Table\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelTable\Core\Columns\DatabaseColumn;
use LaravelTable\Core\DTO\TableStateDTO;
use LaravelTable\Core\Enums\SortDirection;
use LaravelTable\Core\Query\Pipeline\QueryPipeline;
use LaravelTable\Engine\CastManager;
use LaravelTable\Engine\ColumnRegistry;
use LaravelTable\Engine\QueryBuilderEngine;
use LaravelTable\Engine\RowSerializer;
use LaravelTable\Laravel\Query\EloquentQueryEngine;
use LaravelTable\Tests\Fixtures\FakeModel;
use LaravelTable\Tests\Fixtures\FakeTable;
use Mockery;

final class EngineTest extends TestCase
{
    public function test_column_registry_boot_all_and_get(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('getModel')->andReturn(new FakeModel());

        $column = DatabaseColumn::make('age');
        $table = new FakeTable()
            ->setBuilder($builder)
            ->setState(new TableStateDTO(null, SortDirection::ASC, [], null, 1, 15))
            ->setDefinedColumns([$column]);

        $registry = new ColumnRegistry($table);
        $registry->boot();

        $all = $registry->all();
        $this->assertCount(1, $all);
        $this->assertSame($column, $registry->get('age'));
        $this->assertNull($registry->get('missing'));
    }

    public function test_row_serializer_serializes_columns_and_rows(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('getModel')->andReturn(new FakeModel());

        $col1 = DatabaseColumn::make('age');
        $col2 = DatabaseColumn::make('hidden')->visible(false);

        $table = new FakeTable()
            ->setBuilder($builder)
            ->setState(new TableStateDTO(null, SortDirection::ASC, [], null, 1, 15))
            ->setDefinedColumns([$col1, $col2]);

        $registry = new ColumnRegistry($table);
        $registry->boot();

        $serializer = new RowSerializer($registry, new CastManager([]));
        $columns = $serializer->serializeColumns();
        $this->assertCount(1, $columns);
        $this->assertSame('age', $columns[0]['name']);

        $row = $serializer->serialize(['age' => 30, 'hidden' => 'x']);
        $this->assertSame(['age' => 30], $row);
    }

    public function test_query_builder_engine_apply_uses_pipeline(): void
    {
        $pipeline = Mockery::mock(QueryPipeline::class);
        $engine = new QueryBuilderEngine($pipeline);
        $builder = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        $table->shouldReceive('pipes')->once()->andReturn(['pipe']);
        $pipeline->shouldReceive('run')->once()->with($builder, $table, ['pipe'])->andReturn($builder);

        $this->assertSame($builder, $engine->apply($builder, $table));
    }

    public function test_eloquent_query_engine_extends_query_builder_engine(): void
    {
        $engine = new EloquentQueryEngine(Mockery::mock(QueryPipeline::class));
        $this->assertInstanceOf(QueryBuilderEngine::class, $engine);
    }
}
