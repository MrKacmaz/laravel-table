<?php

declare(strict_types=1);

namespace LaravelTable\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelTable\Core\Casting\CastManager;
use LaravelTable\Core\Contracts\CapabilityGate;
use LaravelTable\Core\Contracts\QueryEngine;
use LaravelTable\Core\Contracts\StateResolver;
use LaravelTable\Laravel\Capabilities\LaravelCapabilityGate;
use LaravelTable\Laravel\Query\EloquentQueryEngine;
use LaravelTable\Laravel\Resolvers\HttpTableStateResolver;

class LaravelTableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $configPath = dirname(__DIR__, 3) . '/config/laravel-table.php';

        $this->mergeConfigFrom(
            $configPath,
            'laravel-table'
        );

        $this->app->singleton(CastManager::class, function () {
            return new CastManager(
                config('laravel-table.casters', [])
            );
        });

        $this->app->bind(StateResolver::class, HttpTableStateResolver::class);
        $this->app->bind(QueryEngine::class, EloquentQueryEngine::class);
        $this->app->bind(CapabilityGate::class, LaravelCapabilityGate::class);
    }

    public function boot(): void
    {
        $configPath = dirname(__DIR__, 3) . '/config/laravel-table.php';

        $this->publishes([
            $configPath => config_path(
                'laravel-table.php'
            ),
        ], 'laravel-table-config');
    }

}
