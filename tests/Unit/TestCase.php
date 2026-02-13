<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Mockery;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected Container $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new class extends Container {
            public function configPath(string $path = ''): string
            {
                return __DIR__ . '/../../config' . ($path !== '' ? '/' . $path : '');
            }
        };
        Container::setInstance($this->app);
        Facade::setFacadeApplication($this->app);

        $this->app->instance('config', new Repository([
            'laravel-table' => [
                'per_page' => 15,
                'max_per_page' => 100,
                'casters' => [],
                'pipes' => [],
            ],
        ]));

        $this->app->instance('request', Request::create('/', 'GET'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
