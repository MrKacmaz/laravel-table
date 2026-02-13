<?php

namespace LaravelTable\Laravel\Facades;

use InvalidArgumentException;
use LaravelTable\Core\Contracts\StateResolver;
use LaravelTable\Core\Table\Table;

class TableFactory
{
    public static function make(string $tableClass): Table
    {
        $table = app($tableClass);

        if (! $table instanceof Table) {
            throw new InvalidArgumentException(
                sprintf(
                    'TableFactory expects a %s instance, %s given.',
                    Table::class,
                    get_debug_type($table)
                )
            );
        }

        $table->setState(app(StateResolver::class)->resolve());
        $table->boot();

        return $table;
    }

}
