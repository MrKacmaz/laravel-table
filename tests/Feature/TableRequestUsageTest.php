<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Feature;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Tests\Fixtures\FakeModel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelTable\Core\Casting\CastManager;
use LaravelTable\Core\Contracts\QueryEngine;
use LaravelTable\Core\Contracts\StateResolver;
use LaravelTable\Core\Query\Pipes\ApplyFilters;
use LaravelTable\Core\Query\Pipes\ApplySearch;
use LaravelTable\Core\Query\Pipes\ApplySorting;
use LaravelTable\Core\Query\Pipeline\QueryPipeline;
use LaravelTable\Laravel\Query\EloquentQueryEngine;
use LaravelTable\Laravel\Resolvers\HttpTableStateResolver;
use LaravelTable\Tests\Stubs\ExampleUsersTable;
use LaravelTable\Tests\Unit\TestCase;
use Mockery;

final class TableRequestUsageTest extends TestCase
{
    public function test_request_driven_table_usage_returns_expected_response(): void
    {
        $this->app->instance('request', Request::create('/users/table', 'GET', [
            'sort' => 'id',
            'direction' => 'desc',
            'search' => 'john',
            'filter' => [
                'id' => [
                    'in' => [1, 2],
                ],
            ],
            'page' => 1,
            'per_page' => 2,
        ]));

        config()->set('laravel-table.pipes', [
            ApplyFilters::class,
            ApplySorting::class,
            ApplySearch::class,
        ]);

        $this->app->bind(StateResolver::class, HttpTableStateResolver::class);
        $this->app->bind(
            QueryEngine::class,
            fn (): EloquentQueryEngine => new EloquentQueryEngine(new QueryPipeline())
        );
        $this->app->singleton(CastManager::class, fn (): CastManager => new CastManager([]));

        $relationBuilder = Mockery::mock(Builder::class);
        $relationBuilder
            ->shouldReceive('where')
            ->once()
            ->with('city', 'like', '%john%')
            ->andReturnSelf();

        $searchBuilder = Mockery::mock(Builder::class);
        $searchBuilder
            ->shouldReceive('where')
            ->once()
            ->with('name', 'like', '%john%')
            ->andReturnSelf();
        $searchBuilder
            ->shouldReceive('where')
            ->once()
            ->with('email', 'like', '%john%')
            ->andReturnSelf();
        $searchBuilder
            ->shouldReceive('whereHas')
            ->once()
            ->with('profile', Mockery::type(Closure::class))
            ->andReturnUsing(function (string $relation, Closure $closure) use ($relationBuilder) {
                $this->assertSame('profile', $relation);
                $closure($relationBuilder);

                return null;
            });

        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('getModel')->andReturn(new FakeModel());
        $builder->shouldReceive('whereIn')->once()->with('id', [1, 2])->andReturnSelf();
        $builder->shouldReceive('orderBy')->once()->with('id', 'desc')->andReturnSelf();
        $builder
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::type(Closure::class))
            ->andReturnUsing(function (Closure $closure) use ($builder, $searchBuilder) {
                $closure($searchBuilder);

                return $builder;
            });
        $builder
            ->shouldReceive('paginate')
            ->once()
            ->with(2, ['*'], 'page', 1)
            ->andReturn(
                new LengthAwarePaginator(
                    items: [
                        ['id' => 2, 'name' => 'John Doe', 'email' => 'john@demo.test', 'profile' => ['city' => 'Berlin']],
                        ['id' => 1, 'name' => 'Johnny', 'email' => 'johnny@demo.test', 'profile' => ['city' => 'Ankara']],
                    ],
                    total: 2,
                    perPage: 2,
                    currentPage: 1
                )
            );

        ExampleUsersTable::$builder = $builder;

        $result = ExampleUsersTable::make()->response();

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('columns', $result);
        $this->assertArrayHasKey('links', $result);

        $this->assertSame(2, $result['meta']['total']);
        $this->assertSame(2, $result['meta']['per_page']);
        $this->assertSame(1, $result['meta']['current_page']);
        $this->assertSame(1, $result['meta']['last_page']);

        $this->assertCount(2, $result['data']);
        $this->assertSame('John Doe', $result['data'][0]['name']);
        $this->assertSame('Berlin', $result['data'][0]['profile.city']);

        $columnNames = array_column($result['columns'], 'name');
        $this->assertSame(['id', 'name', 'email', 'profile.city'], $columnNames);
    }
}
