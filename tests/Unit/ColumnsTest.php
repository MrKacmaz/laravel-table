<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\Columns\ComputedColumn;
use LaravelTable\Core\Columns\DatabaseColumn;
use LaravelTable\Core\Columns\RelationColumn;
use LaravelTable\Core\Enums\FilterOperator;
use LaravelTable\Core\Enums\SortDirection;
use LaravelTable\Tests\Fixtures\DummyColumn;
use LaravelTable\Tests\Fixtures\FakeModel;
use LaravelTable\Tests\Fixtures\FakeTable;
use Mockery;
use RuntimeException;

final class ColumnsTest extends TestCase
{
    public function test_base_column_configuration_methods_and_boot_cast_detection(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('getModel')->andReturn(new FakeModel());

        $table = new FakeTable()->setBuilder($builder);
        $column = new DummyColumn('age');

        $column
            ->label('Age Label')
            ->sortable()
            ->searchable()
            ->filterable()
            ->visible()
            ->formattable(false)
            ->formatWith('int');

        $column->setTable($table);
        $column->boot();

        $this->assertSame('age', $column->getName());
        $this->assertSame('Age Label', $column->getLabel());
        $this->assertTrue($column->isSortable());
        $this->assertTrue($column->isSearchable());
        $this->assertTrue($column->isFilterable());
        $this->assertTrue($column->isVisible());
        $this->assertSame('integer', $column->getCast());
        $this->assertSame(12, $column->resolve(['age' => 12]));
        $this->assertSame('Age Label', $column->toArray()['label']);
    }

    public function test_base_column_default_apply_methods_throw(): void
    {
        $builder = Mockery::mock(Builder::class);
        $column = new DummyColumn('name');

        try {
            $column->applySearch($builder, 'x');
            $this->fail('Expected RuntimeException for applySearch.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        }

        try {
            $column->applySort($builder, SortDirection::ASC);
            $this->fail('Expected RuntimeException for applySort.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        }

        $this->expectException(RuntimeException::class);
        $column->applyFilter($builder, FilterOperator::EQ, 'x');
    }

    public function test_database_column_methods(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('orderBy')->once()->with('name', 'asc')->andReturnSelf();
        $builder->shouldReceive('where')->once()->with('name', 'like', '%john%')->andReturnSelf();
        $builder->shouldReceive('whereIn')->once()->with('name', ['a', 'b'])->andReturnSelf();
        $builder->shouldReceive('whereNotIn')->once()->with('name', ['a'])->andReturnSelf();
        $builder->shouldReceive('whereBetween')->once()->with('name', ['a', 'z'])->andReturnSelf();
        $builder->shouldReceive('where')->once()->with('name', '=', 'john')->andReturnSelf();

        $column = DatabaseColumn::make('name')->sortable()->searchable()->filterable();

        $this->assertSame('john', $column->resolve(['name' => 'john']));
        $column->applySort($builder, SortDirection::ASC);
        $column->applySearch($builder, 'john');
        $column->applyFilter($builder, FilterOperator::IN, ['a', 'b']);
        $column->applyFilter($builder, FilterOperator::NOT_IN, ['a']);
        $column->applyFilter($builder, FilterOperator::BETWEEN, ['a', 'z']);
        $column->applyFilter($builder, FilterOperator::EQ, 'john');
    }

    public function test_computed_column_methods(): void
    {
        $builder = Mockery::mock(Builder::class);
        $column = ComputedColumn::make('full_name', fn (array $row): string => $row['first'] . ' ' . $row['last']);

        $this->assertSame('Jane Doe', $column->resolve(['first' => 'Jane', 'last' => 'Doe']));

        $this->expectException(RuntimeException::class);
        $column->sortable()->applySort($builder, SortDirection::ASC);
    }

    public function test_computed_column_search_and_filter_throw_when_enabled(): void
    {
        $builder = Mockery::mock(Builder::class);
        $column = ComputedColumn::make('total', fn (): int => 10);

        try {
            $column->searchable()->applySearch($builder, '10');
            $this->fail('Expected RuntimeException for searchable computed column.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        }

        $this->expectException(RuntimeException::class);
        $column->filterable()->applyFilter($builder, FilterOperator::EQ, 10);
    }

    public function test_relation_column_methods(): void
    {
        $relationBuilder = Mockery::mock(Builder::class);
        $relationBuilder->shouldReceive('where')->once()->with('name', 'like', '%john%')->andReturnSelf();
        $relationBuilder->shouldReceive('whereIn')->once()->with('name', ['a'])->andReturnSelf();
        $relationBuilder->shouldReceive('whereNotIn')->once()->with('name', ['b'])->andReturnSelf();
        $relationBuilder->shouldReceive('whereBetween')->once()->with('name', [1, 5])->andReturnSelf();
        $relationBuilder->shouldReceive('where')->once()->with('name', '=', 'john')->andReturnSelf();

        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('withAggregate')->once()->with('profile', 'name')->andReturnSelf();
        $builder->shouldReceive('orderBy')->once()->with('profile_name', 'desc')->andReturnSelf();
        $builder->shouldReceive('whereHas')->times(5)->andReturnUsing(
            function (string $relation, Closure $closure) use ($relationBuilder): null {
                $this->assertSame('profile', $relation);
                $closure($relationBuilder);
                return null;
            }
        );

        $column = RelationColumn::make('profile', 'name')->sortable()->searchable()->filterable();

        $this->assertSame('profile', $column->getRelation());
        $this->assertSame('name', $column->getField());
        $this->assertSame('john', $column->resolve(['profile' => ['name' => 'john']]));

        $column->applySort($builder, SortDirection::DESC);
        $column->applySearch($builder, 'john');
        $column->applyFilter($builder, FilterOperator::IN, ['a']);
        $column->applyFilter($builder, FilterOperator::NOT_IN, ['b']);
        $column->applyFilter($builder, FilterOperator::BETWEEN, [1, 5]);
        $column->applyFilter($builder, FilterOperator::EQ, 'john');
    }
}
