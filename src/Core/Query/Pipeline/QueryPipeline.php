<?php

namespace LaravelTable\Core\Query\Pipeline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;
use LaravelTable\Core\Table\Table;

class QueryPipeline
{
    /**
     * @param  array<int, class-string|object>  $pipes
     */
    public function run(Builder $builder, Table $table, array $pipes): Builder
    {
        $resolvedPipes = array_map(
            fn ($pipe) => is_string($pipe)
                ? app()->make($pipe, ['table' => $table])
                : $pipe,
            $pipes
        );

        return app(Pipeline::class)
            ->send($builder)
            ->through($resolvedPipes)
            ->thenReturn();
    }

}
