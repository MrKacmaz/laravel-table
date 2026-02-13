<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use LaravelTable\Core\Table\Table;
use LaravelTable\Laravel\Facades\TableFactory;
use LaravelTable\Laravel\Providers\LaravelTableServiceProvider;
use LaravelTable\Core\Enums\FilterOperator;
use LaravelTable\Core\Enums\SortDirection;

final class SmokeTest extends TestCase
{
    public function test_core_classes_are_autoloadable(): void
    {
        $this->assertTrue(class_exists(Table::class));
        $this->assertTrue(class_exists(TableFactory::class));
        $this->assertTrue(class_exists(LaravelTableServiceProvider::class));
    }

    public function test_sort_direction_from_request(): void
    {
        $this->assertSame(SortDirection::ASC, SortDirection::fromRequest('asc'));
        $this->assertSame(SortDirection::DESC, SortDirection::fromRequest('desc'));
        $this->assertSame(SortDirection::ASC, SortDirection::fromRequest('invalid'));
    }

    public function test_filter_operator_enum_values(): void
    {
        $this->assertTrue(FilterOperator::hasValue('in'));
        $this->assertTrue(FilterOperator::hasValue('not_in'));
        $this->assertTrue(FilterOperator::hasValue('between'));
    }
}
