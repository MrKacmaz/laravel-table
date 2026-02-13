<?php

declare(strict_types=1);

namespace LaravelTable\Core\Support;

final class Strings
{
    public static function headline(string $value): string
    {
        return str($value)->headline()->toString();
    }
}
