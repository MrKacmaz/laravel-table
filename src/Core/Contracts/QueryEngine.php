<?php

namespace LaravelTable\Core\Contracts;

use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\Table\Table;

interface QueryEngine
{
    public function apply(Builder $query, Table $table): Builder;

}
