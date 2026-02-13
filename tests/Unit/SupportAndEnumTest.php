<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use LaravelTable\Core\Enums\FilterOperator;
use LaravelTable\Core\Enums\SortDirection;
use LaravelTable\Core\Support\Arrays;
use LaravelTable\Core\Support\Strings;

final class SupportAndEnumTest extends TestCase
{
    public function test_arrays_helpers(): void
    {
        $this->assertTrue(Arrays::isAssoc(['a' => 1, 'b' => 2]));
        $this->assertFalse(Arrays::isAssoc([1, 2, 3]));
        $this->assertSame(['x'], Arrays::wrap('x'));
        $this->assertSame(['x' => 1], Arrays::wrap(['x' => 1]));
    }

    public function test_strings_headline(): void
    {
        $this->assertSame('User Name', Strings::headline('user_name'));
    }

    public function test_sort_direction_methods(): void
    {
        $this->assertSame(SortDirection::ASC, SortDirection::fromRequest('asc'));
        $this->assertSame(SortDirection::DESC, SortDirection::fromRequest('desc'));
        $this->assertTrue(SortDirection::ASC->isAsc());
        $this->assertTrue(SortDirection::ASC->is(SortDirection::ASC));
        $this->assertTrue(SortDirection::ASC->isNot(SortDirection::DESC));
        $this->assertTrue(SortDirection::ASC->in(['asc']));
        $this->assertTrue(SortDirection::ASC->notIn(['desc']));
        $this->assertTrue(SortDirection::hasName('ASC'));
        $this->assertTrue(SortDirection::hasValue('asc'));
        $this->assertSame(['ASC', 'DESC'], SortDirection::getNames());
        $this->assertSame(['asc', 'desc'], SortDirection::getValues());
        $this->assertSame(['asc' => 'ASC', 'desc' => 'DESC'], SortDirection::toArray());
    }

    public function test_filter_operator_methods(): void
    {
        $this->assertTrue(FilterOperator::IN->is(FilterOperator::IN));
        $this->assertTrue(FilterOperator::IN->isNot(FilterOperator::NOT_IN));
        $this->assertTrue(FilterOperator::IN->in(['in', FilterOperator::EQ]));
        $this->assertTrue(FilterOperator::IN->notIn(['between']));
        $this->assertTrue(FilterOperator::hasName('BETWEEN'));
        $this->assertTrue(FilterOperator::hasValue('between'));
        $this->assertContains('BETWEEN', FilterOperator::getNames());
        $this->assertContains('between', FilterOperator::getValues());
        $this->assertArrayHasKey('between', FilterOperator::toArray());
    }
}
