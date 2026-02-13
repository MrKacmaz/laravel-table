<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Fixtures;

use LaravelTable\Core\Columns\BaseColumn;

final class DummyColumn extends BaseColumn
{
    public function resolve(mixed $row): mixed
    {
        return data_get($row, $this->name);
    }
}
