<?php

declare(strict_types=1);

namespace LaravelTable\Core\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelTable\Core\Table\Table;

class QueryContext
{
    /**
     * @param Builder<Model> $builder
     */
    public function __construct(
        public Table $table,
        public Builder $builder
    ) {
    }

}
