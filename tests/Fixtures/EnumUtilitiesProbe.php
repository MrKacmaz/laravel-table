<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Fixtures;

use LaravelTable\Core\Support\EnumUtilities;

final class EnumUtilitiesProbe
{
    use EnumUtilities;

    public function __construct(public string $value)
    {
    }
}
