<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use ReflectionClass;
use LaravelTable\Core\Contracts\CapabilityGate;
use LaravelTable\Core\Contracts\ColumnContract;
use LaravelTable\Core\Contracts\QueryEngine;
use LaravelTable\Core\Contracts\StateResolver;
use LaravelTable\Core\Contracts\TableContract;
use LaravelTable\Core\Contracts\ValueCaster;
use LaravelTable\Core\DTO\TableResponseDTO;
use LaravelTable\Core\DTO\TableStateDTO;
use LaravelTable\Core\Enums\SortDirection;

final class DtoAndContractTest extends TestCase
{
    public function test_table_state_dto_constructor_values(): void
    {
        $dto = new TableStateDTO(
            sort: 'name',
            direction: SortDirection::DESC,
            filters: ['status' => ['=' => 'active']],
            search: 'john',
            page: 2,
            perPage: 25
        );

        $this->assertSame('name', $dto->sort);
        $this->assertSame(SortDirection::DESC, $dto->direction);
        $this->assertSame(['status' => ['=' => 'active']], $dto->filters);
        $this->assertSame('john', $dto->search);
        $this->assertSame(2, $dto->page);
        $this->assertSame(25, $dto->perPage);
    }

    public function test_table_response_dto_to_array(): void
    {
        $dto = new TableResponseDTO(
            data: [['id' => 1]],
            meta: ['total' => 1],
            columns: [['name' => 'id']],
            links: ['prev' => null, 'next' => '/?page=2']
        );

        $this->assertSame(
            [
                'data' => [['id' => 1]],
                'meta' => ['total' => 1],
                'columns' => [['name' => 'id']],
                'links' => ['prev' => null, 'next' => '/?page=2'],
            ],
            $dto->toArray()
        );
    }

    public function test_contracts_expose_expected_methods(): void
    {
        $contracts = [
            CapabilityGate::class => ['allows'],
            StateResolver::class => ['resolve'],
            QueryEngine::class => ['apply'],
            TableContract::class => ['getState', 'columns', 'getColumn', 'paginate', 'response'],
            ColumnContract::class => ['boot', 'resolve', 'getName', 'getCast', 'setTable', 'isSortable', 'isSearchable', 'isFilterable', 'isVisible', 'applySort', 'applySearch', 'applyFilter', 'toArray'],
            ValueCaster::class => ['cast'],
        ];

        foreach ($contracts as $contract => $methods) {
            $reflection = new ReflectionClass($contract);
            $this->assertTrue($reflection->isInterface());

            foreach ($methods as $method) {
                $this->assertTrue($reflection->hasMethod($method), "{$contract}::{$method} missing");
            }
        }
    }
}
