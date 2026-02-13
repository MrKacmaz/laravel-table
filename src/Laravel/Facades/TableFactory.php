<?php

declare(strict_types=1);

namespace LaravelTable\Laravel\Facades;

use LaravelTable\Core\Contracts\StateResolver;
use LaravelTable\Core\Table\Table;

class TableFactory
{
    /**
     * @param class-string<Table> $tableClass
     */
    public static function make(string $tableClass): Table
    {
        /** @var Table $table */
        $table = app($tableClass);

        $table->setState(app(StateResolver::class)->resolve());
        $table->boot();

        return $table;
    }

}
