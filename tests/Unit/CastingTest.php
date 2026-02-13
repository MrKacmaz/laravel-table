<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Unit;

use LaravelTable\Core\Casting\CastManager as CoreCastManager;
use LaravelTable\Core\Casting\CastTypeNormalizer;
use LaravelTable\Core\Casting\Casters\BoolCaster;
use LaravelTable\Core\Casting\Casters\FloatCaster;
use LaravelTable\Core\Casting\Casters\IntCaster;
use LaravelTable\Core\Contracts\ColumnContract;
use LaravelTable\Engine\CastManager;
use Mockery;

final class CastingTest extends TestCase
{
    public function test_cast_type_normalizer(): void
    {
        $normalizer = new CastTypeNormalizer();

        $this->assertNull($normalizer->normalize(null));
        $this->assertNull($normalizer->normalize(''));
        $this->assertSame('int', $normalizer->normalize('int'));
        $this->assertSame('decimal', $normalizer->normalize('decimal:2'));
    }

    public function test_primitive_casters(): void
    {
        $column = Mockery::mock(ColumnContract::class);

        $this->assertSame(5, new IntCaster()->cast('5', $column, []));
        $this->assertNull(new IntCaster()->cast(null, $column, []));

        $this->assertSame(3.5, new FloatCaster()->cast('3.5', $column, []));
        $this->assertNull(new FloatCaster()->cast(null, $column, []));

        $this->assertTrue(new BoolCaster()->cast(1, $column, []));
        $this->assertFalse(new BoolCaster()->cast(0, $column, []));
    }

    public function test_engine_cast_manager_returns_original_for_unknown_or_null_type(): void
    {
        $manager = new CastManager([]);
        $column = Mockery::mock(ColumnContract::class);

        $this->assertSame('abc', $manager->cast(null, 'abc', $column, []));
        $this->assertSame('abc', $manager->cast('missing', 'abc', $column, []));
    }

    public function test_engine_cast_manager_uses_resolved_caster(): void
    {
        $this->app->bind('test.int.caster', IntCaster::class);
        $manager = new CastManager(['int' => 'test.int.caster']);
        $column = Mockery::mock(ColumnContract::class);

        $this->assertSame(42, $manager->cast('int', '42', $column, []));
        $this->assertSame(7, $manager->cast('int:nullable', '7', $column, []));
    }

    public function test_core_cast_manager_extends_engine(): void
    {
        $core = new CoreCastManager([]);
        $this->assertInstanceOf(CastManager::class, $core);
    }
}
