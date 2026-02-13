<?php

declare(strict_types=1);

namespace LaravelTable\Core\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelTable\Core\Table\Table;

interface QueryEngine
{
    /**
     * @param Builder<Model> $query
     * @return Builder<Model>
     */
    public function apply(Builder $query, Table $table): Builder;

}
