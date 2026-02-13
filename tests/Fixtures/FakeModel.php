<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

final class FakeModel extends Model
{
    protected $table = 'fake_models';

    protected $casts = [
        'age' => 'integer',
        'is_active' => 'boolean',
    ];
}
