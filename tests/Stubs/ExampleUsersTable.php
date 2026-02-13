<?php

declare(strict_types=1);

namespace LaravelTable\Tests\Stubs;

use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\Columns\DatabaseColumn;
use LaravelTable\Core\Columns\RelationColumn;
use LaravelTable\Core\Table\Table;

final class ExampleUsersTable extends Table
{
    public static Builder $builder;

    protected function query(): Builder
    {
        return self::$builder;
    }

    protected function defineColumns(): array
    {
        return [
            DatabaseColumn::make('id')->label('ID')->sortable()->filterable(),
            DatabaseColumn::make('name')->searchable()->sortable(),
            DatabaseColumn::make('email')->searchable(),
            RelationColumn::make('profile', 'city')->label('City')->searchable()->filterable(),
        ];
    }
}
