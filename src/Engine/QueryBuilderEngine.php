<?php

declare(strict_types=1);

namespace LaravelTable\Engine;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelTable\Core\Contracts\QueryEngine;
use LaravelTable\Core\Query\Pipeline\QueryPipeline;
use LaravelTable\Core\Table\Table;

class QueryBuilderEngine implements QueryEngine
{
    public function __construct(protected QueryPipeline $pipeline)
    {
    }

    /**
     * @param Builder<Model> $query
     * @return Builder<Model>
     */
    public function apply(Builder $query, Table $table): Builder
    {
        return $this->pipeline->run($query, $table, $table->pipes());
    }

}
