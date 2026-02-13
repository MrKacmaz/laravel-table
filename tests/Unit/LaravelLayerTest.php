<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use LaravelTable\Core\Casting\CastManager;
use LaravelTable\Core\Contracts\QueryEngine;
use LaravelTable\Core\Contracts\StateResolver;
use LaravelTable\Core\DTO\TableStateDTO;
use LaravelTable\Core\Enums\SortDirection;
use LaravelTable\Laravel\Capabilities\LaravelCapabilityGate;
use LaravelTable\Laravel\Facades\TableFactory;
use LaravelTable\Laravel\Providers\LaravelTableServiceProvider;
use LaravelTable\Laravel\Resolvers\HttpTableStateResolver;
use LaravelTable\Tests\Fixtures\FakeModel;
use LaravelTable\Tests\Fixtures\FakeTable;
use Mockery;

final class LaravelLayerTest extends TestCase
{
    public function test_http_table_state_resolver_resolve(): void
    {
        $this->app->instance(
            'request',
            Request::create(
                '/',
                'GET',
                [
                    'sort' => 'name',
                    'direction' => 'desc',
                    'filter' => ['status' => ['=' => 'active']],
                    'search' => 'john',
                    'page' => 3,
                    'per_page' => 20,
                ]
            )
        );

        $this->app->instance('config', new Repository([
            'laravel-table' => [
                'per_page' => 15,
                'max_per_page' => 50,
            ],
        ]));

        $state = new HttpTableStateResolver()->resolve();

        $this->assertSame('name', $state->sort);
        $this->assertSame(SortDirection::DESC, $state->direction);
        $this->assertSame(['status' => ['=' => 'active']], $state->filters);
        $this->assertSame('john', $state->search);
        $this->assertSame(3, $state->page);
        $this->assertSame(20, $state->perPage);
    }

    public function test_http_table_state_resolver_handles_array_direction(): void
    {
        $this->app->instance(
            'request',
            Request::create(
                '/',
                'GET',
                [
                    'direction' => ['desc', 'asc'], // Array input should not cause issues
                ]
            )
        );

        $this->app->instance('config', new Repository([
            'laravel-table' => [
                'per_page' => 15,
                'max_per_page' => 50,
            ],
        ]));

        $state = new HttpTableStateResolver()->resolve();

        // Should default to ASC when an array is passed
        $this->assertSame(SortDirection::ASC, $state->direction);
    }

    public function test_table_factory_make(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('getModel')->andReturn(new FakeModel());

        $table = new FakeTable()
            ->setBuilder($builder)
            ->setDefinedColumns([]);

        $this->app->bind(FakeTable::class, fn (): FakeTable => $table);
        $this->app->instance(
            StateResolver::class,
            new class () implements StateResolver {
                public function resolve(): TableStateDTO
                {
                    return new TableStateDTO(null, SortDirection::ASC, [], null, 1, 15);
                }
            }
        );
        $this->app->instance(CastManager::class, new CastManager([]));

        $resolved = TableFactory::make(FakeTable::class);
        $this->assertInstanceOf(FakeTable::class, $resolved);
        $this->assertSame(1, $resolved->getState()->page);
    }

    public function test_table_static_make_uses_factory_flow(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('getModel')->andReturn(new FakeModel());

        $table = new FakeTable()
            ->setBuilder($builder)
            ->setDefinedColumns([]);

        $this->app->bind(FakeTable::class, fn (): FakeTable => $table);
        $this->app->instance(
            StateResolver::class,
            new class () implements StateResolver {
                public function resolve(): TableStateDTO
                {
                    return new TableStateDTO(null, SortDirection::ASC, [], null, 1, 15);
                }
            }
        );
        $this->app->instance(CastManager::class, new CastManager([]));

        $resolved = FakeTable::make();
        $this->assertInstanceOf(FakeTable::class, $resolved);
    }

    public function test_laravel_capability_gate_allows(): void
    {
        $gateMock = Mockery::mock(GateContract::class);
        $gateMock->shouldReceive('allows')->once()->with('view-table', ['id' => 1])->andReturn(true);
        $this->app->instance(GateContract::class, $gateMock);

        $gate = new LaravelCapabilityGate();
        $this->assertTrue($gate->allows('view-table', ['id' => 1]));
    }

    public function test_service_provider_register_and_boot(): void
    {
        $provider = new class ($this->app) extends LaravelTableServiceProvider {
            public array $merged = [];
            public array $published = [];

            protected function mergeConfigFrom($path, $key): void
            {
                $this->merged = [$path, $key];
            }

            public function publishes(array $paths, $groups = null): void
            {
                $this->published = [$paths, $groups];
            }
        };

        $provider->register();
        $this->assertNotEmpty($provider->merged);
        $this->assertTrue($this->app->bound(StateResolver::class));
        $this->assertTrue($this->app->bound(QueryEngine::class));

        $provider->boot();
        $this->assertNotEmpty($provider->published);
    }
}
