<?php

declare(strict_types=1);

return [

    'casters' => [
        'int'     => \LaravelTable\Core\Casting\Casters\IntCaster::class,
        'integer' => \LaravelTable\Core\Casting\Casters\IntCaster::class,

        'real'    => \LaravelTable\Core\Casting\Casters\FloatCaster::class,
        'float'   => \LaravelTable\Core\Casting\Casters\FloatCaster::class,
        'double'  => \LaravelTable\Core\Casting\Casters\FloatCaster::class,
        'decimal' => \LaravelTable\Core\Casting\Casters\FloatCaster::class,

        'bool'    => \LaravelTable\Core\Casting\Casters\BoolCaster::class,
        'boolean' => \LaravelTable\Core\Casting\Casters\BoolCaster::class,
    ],

    'pipes' => [
        \LaravelTable\Core\Query\Pipes\ApplyFilters::class,
        \LaravelTable\Core\Query\Pipes\ApplySorting::class,
        \LaravelTable\Core\Query\Pipes\ApplySearch::class,
    ],

];
