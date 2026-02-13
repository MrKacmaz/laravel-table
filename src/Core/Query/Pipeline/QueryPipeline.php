<?php

declare(strict_types=1);

namespace LaravelTable\Core\Query\Pipeline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;
use LaravelTable\Core\Table\Table;

class QueryPipeline
{
    /**
     * @param Builder<Model> $builder
     * @param  array<int, class-string|object>  $pipes
     * @return Builder<Model>
     */
    public function run(Builder $builder, Table $table, array $pipes): Builder
    {
        $resolvedPipes = array_map(
            fn (string|object $pipe) => is_string($pipe)
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
