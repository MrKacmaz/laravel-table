<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\Contracts\ColumnContract;
use LaravelTable\Core\DTO\TableStateDTO;
use LaravelTable\Core\Enums\SortDirection;
use LaravelTable\Core\Query\Pipeline\QueryPipeline;
use LaravelTable\Core\Query\Pipes\ApplyFilters;
use LaravelTable\Core\Query\Pipes\ApplySearch;
use LaravelTable\Core\Query\Pipes\ApplySorting;
use LaravelTable\Core\Query\QueryContext;
use LaravelTable\Core\Table\Table;
use Mockery;

final class QueryPipelineAndPipesTest extends TestCase
{
    public function test_query_context_constructor(): void
    {
        $table = Mockery::mock(Table::class);
        $builder = Mockery::mock(Builder::class);
        $context = new QueryContext($table, $builder);

        $this->assertSame($table, $context->table);
        $this->assertSame($builder, $context->builder);
    }

    public function test_query_pipeline_run_executes_pipe_chain(): void
    {
        $table = Mockery::mock(Table::class);
        $builder = Mockery::mock(Builder::class);
        $pipeline = new QueryPipeline();

        $pipe = new class ($table) {
            public function __construct(public Table $table)
            {
            }

            public function handle(Builder $query, Closure $next): mixed
            {
                return $next($query);
            }
        };

        $result = $pipeline->run($builder, $table, [$pipe]);
        $this->assertSame($builder, $result);
    }

    public function test_apply_sorting_pipe_calls_column_sort(): void
    {
        $table = Mockery::mock(Table::class);
        $state = new TableStateDTO('name', SortDirection::DESC, [], null, 1, 15);
        $column = Mockery::mock(ColumnContract::class);
        $builder = Mockery::mock(Builder::class);

        $table->shouldReceive('getState')->andReturn($state);
        $table->shouldReceive('getColumn')->with('name')->andReturn($column);
        $column->shouldReceive('isSortable')->andReturn(true);
        $column->shouldReceive('applySort')->once()->with($builder, SortDirection::DESC);

        $pipe = new ApplySorting($table);
        $returned = $pipe->handle($builder, fn ($q) => $q);
        $this->assertSame($builder, $returned);
    }

    public function test_apply_search_pipe_runs_on_searchable_columns(): void
    {
        $table = Mockery::mock(Table::class);
        $state = new TableStateDTO(null, SortDirection::ASC, [], 'john', 1, 15);
        $columnSearchable = Mockery::mock(ColumnContract::class);
        $columnIgnored = Mockery::mock(ColumnContract::class);
        $builder = Mockery::mock(Builder::class);

        $table->shouldReceive('getState')->andReturn($state);
        $table->shouldReceive('columns')->andReturn([$columnSearchable, $columnIgnored]);
        $columnSearchable->shouldReceive('isSearchable')->andReturn(true);
        $columnSearchable->shouldReceive('applySearch')->once();
        $columnIgnored->shouldReceive('isSearchable')->andReturn(false);

        $builder->shouldReceive('where')->once()->andReturnUsing(function (Closure $closure) use ($builder) {
            $closure($builder);
            return $builder;
        });

        $pipe = new ApplySearch($table);
        $returned = $pipe->handle($builder, fn ($q) => $q);
        $this->assertSame($builder, $returned);
    }

    public function test_apply_filters_pipe_calls_column_apply_filter(): void
    {
        $table = Mockery::mock(Table::class);
        $state = new TableStateDTO(
            null,
            SortDirection::ASC,
            ['status' => ['=' => 'active']],
            null,
            1,
            15
        );
        $column = Mockery::mock(ColumnContract::class);
        $builder = Mockery::mock(Builder::class);

        $table->shouldReceive('getState')->andReturn($state);
        $table->shouldReceive('getColumn')->with('status')->andReturn($column);
        $column->shouldReceive('isFilterable')->andReturn(true);
        $column->shouldReceive('applyFilter')->once();

        $pipe = new ApplyFilters($table);
        $returned = $pipe->handle($builder, fn ($q) => $q);
        $this->assertSame($builder, $returned);
    }
}
