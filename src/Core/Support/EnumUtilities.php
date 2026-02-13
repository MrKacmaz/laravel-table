<?php

declare(strict_types=1);

namespace LaravelTable\Core\Support;

trait EnumUtilities
{
    public function is(self $enum): bool
    {
        return $this === $enum;
    }

    public function isNot(self $enum): bool
    {
        return $this !== $enum;
    }

    public function in(iterable $values): bool
    {
        foreach ($values as $value) {
            if ($value instanceof self && $this->is($value)) {
                return true;
            }

            if ($this->value === $value) {
                return true;
            }
        }

        return false;
    }

    public function notIn(iterable $values): bool
    {
        return $this->in($values) === false;
    }

    public static function hasName(string $name): bool
    {
        return in_array($name, array_column(self::cases(), 'name'), true);
    }

    public static function hasValue(mixed $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'), true);
    }

    public static function getNames(?array $cases = null): array
    {
        return array_column($cases ?? self::cases(), 'name');
    }

    public static function getValues(?array $cases = null): array
    {
        return array_column($cases ?? self::cases(), 'value');
    }

    public static function toArray(?array $cases = null): array
    {
        return array_combine(self::getValues($cases), self::getNames($cases));
    }

}
