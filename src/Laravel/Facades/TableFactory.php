<?php

declare(strict_types=1);

namespace LaravelTable\Laravel\Facades;

use LaravelTable\Core\Contracts\StateResolver;
use LaravelTable\Core\Table\Table;
use RuntimeException;

class TableFactory
{
    /**
     * @param class-string<Table> $tableClass
     */
    public static function make(string $tableClass): Table
    {
        $table = app($tableClass);

        if (!$table instanceof Table) {
            throw new RuntimeException(
                sprintf(
                    'The container binding for [%s] must resolve to an instance of %s, got %s instead.',
                    $tableClass,
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
