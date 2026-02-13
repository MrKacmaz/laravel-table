<?php

declare(strict_types=1);

use LaravelTable\Core\Casting\Casters\IntCaster;
use LaravelTable\Core\Casting\Casters\FloatCaster;
use LaravelTable\Core\Casting\Casters\BoolCaster;
use LaravelTable\Core\Query\Pipes\ApplyFilters;
use LaravelTable\Core\Query\Pipes\ApplySorting;
use LaravelTable\Core\Query\Pipes\ApplySearch;

return [

    'casters' => [
        'int'     => IntCaster::class,
        'integer' => IntCaster::class,

        'real'    => FloatCaster::class,
        'float'   => FloatCaster::class,
        'double'  => FloatCaster::class,
        'decimal' => FloatCaster::class,

        'bool'    => BoolCaster::class,
        'boolean' => BoolCaster::class,
    ],

    'pipes' => [
        ApplyFilters::class,
        ApplySorting::class,
        ApplySearch::class,
    ],

];
