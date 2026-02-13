<?php

namespace LaravelTable\Core\Query;

use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\Table\Table;

class QueryContext
{
    public function __construct(
        public Table $table,
        public Builder $builder
    ) {
    }

}
