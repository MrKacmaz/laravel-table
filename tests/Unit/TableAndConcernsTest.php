<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use LaravelTable\Core\Contracts\QueryEngine;
use LaravelTable\Core\Columns\Registry\ColumnRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelTable\Core\Casting\CastManager;
use LaravelTable\Core\Columns\DatabaseColumn;
use LaravelTable\Core\Query\Pipeline\QueryPipeline;
use LaravelTable\Core\Table\Boot\ColumnBooter;
use LaravelTable\Core\Table\Boot\TableBooter;
use LaravelTable\Core\DTO\TableStateDTO;
use LaravelTable\Core\Enums\SortDirection;
use LaravelTable\Engine\QueryBuilderEngine;
use LaravelTable\Tests\Fixtures\FakeModel;
use LaravelTable\Tests\Fixtures\FakeTable;
use Mockery;

final class TableAndConcernsTest extends TestCase
{
    public function test_table_boot_getters_and_builder_flow(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('getModel')->andReturn(new FakeModel());
        $builder->shouldReceive('paginate')
            ->once()
            ->with(15, ['*'], 'page', 1)
            ->andReturn(new LengthAwarePaginator([['name' => 'Jane']], 1, 15, 1));

        $table = new FakeTable()
            ->setBuilder($builder)
            ->setDefinedColumns([DatabaseColumn::make('name')])
            ->setState(new TableStateDTO(null, SortDirection::ASC, [], null, 1, 15));

        $this->app->instance(CastManager::class, new CastManager([]));
        $this->app->instance(
            QueryEngine::class,
            new QueryBuilderEngine(new QueryPipeline())
        );

        $table->boot();

        $this->assertCount(1, $table->columns());
        $this->assertNotNull($table->getColumn('name'));
        $this->assertNull($table->getColumn('missing'));
        $this->assertSame($builder, $table->getBuilder());
        $this->assertTrue($table->beforeCalled);
        $this->assertInstanceOf(FakeModel::class, $table->getModelInstance());

        $paginator = $table->paginate();
        $this->assertSame(1, $paginator->total());
        $this->assertTrue($table->afterCalled);
    }

    public function test_has_response_related_methods(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('getModel')->andReturn(new FakeModel());
        $builder->shouldReceive('paginate')
            ->once()
            ->andReturn(new LengthAwarePaginator([['name' => 'Jane']], 1, 15, 1));

        $table = new FakeTable()
            ->setBuilder($builder)
            ->setDefinedColumns([DatabaseColumn::make('name')])
            ->setState(new TableStateDTO(null, SortDirection::ASC, [], null, 1, 15));

        $this->app->instance(CastManager::class, new CastManager([]));
        $this->app->instance(
            QueryEngine::class,
            new QueryBuilderEngine(new QueryPipeline())
        );

        $table->boot();
        $response = $table->response();

        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('meta', $response);
        $this->assertArrayHasKey('columns', $response);
        $this->assertArrayHasKey('links', $response);
        $this->assertSame([['name' => 'Jane']], $response['data']);
    }

    public function test_table_and_column_booters(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('getModel')->andReturn(new FakeModel());

        $table = new FakeTable()
            ->setBuilder($builder)
            ->setDefinedColumns([DatabaseColumn::make('name')])
            ->setState(new TableStateDTO(null, SortDirection::ASC, [], null, 1, 15));

        $this->app->instance(CastManager::class, new CastManager([]));

        $tableBooter = new TableBooter();
        $tableBooter->boot($table);
        $this->assertCount(1, $table->columns());

        $registry = new ColumnRegistry($table);
        $columnBooter = new ColumnBooter();
        $columnBooter->boot($registry);
        $this->assertCount(1, $registry->all());
    }
}
