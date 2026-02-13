<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use ReflectionClass;
use LaravelTable\Core\Casting\CastManager as CoreCastManager;
use LaravelTable\Core\Columns\DatabaseColumn;
use LaravelTable\Core\Columns\Registry\ColumnRegistry as CoreColumnRegistry;
use LaravelTable\Core\Query\Pipeline\Pipe;
use LaravelTable\Core\Serialization\ColumnSerializer;
use LaravelTable\Core\Serialization\RowSerializer as CoreRowSerializer;
use LaravelTable\Engine\CastManager;
use LaravelTable\Engine\ColumnRegistry;
use LaravelTable\Engine\RowSerializer;

final class CoreWrappersAndSerializerTest extends TestCase
{
    public function test_column_serializer_serialize(): void
    {
        $serializer = new ColumnSerializer();
        $column = DatabaseColumn::make('name')->label('Name');

        $data = $serializer->serialize($column);
        $this->assertSame('name', $data['name']);
        $this->assertSame('Name', $data['label']);
    }

    public function test_core_wrapper_classes_extend_engine_classes(): void
    {
        $this->assertTrue(is_subclass_of(CoreCastManager::class, CastManager::class));
        $this->assertTrue(is_subclass_of(CoreColumnRegistry::class, ColumnRegistry::class));
        $this->assertTrue(is_subclass_of(CoreRowSerializer::class, RowSerializer::class));
    }

    public function test_pipe_contract_shape(): void
    {
        $reflection = new ReflectionClass(Pipe::class);
        $this->assertTrue($reflection->isInterface());
        $this->assertTrue($reflection->hasMethod('handle'));
    }
}
