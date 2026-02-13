<?php

declare(strict_types=1);

namespace LaravelTable\Laravel\Resolvers;

use LaravelTable\Core\Contracts\StateResolver;
use LaravelTable\Core\DTO\TableStateDTO;
use LaravelTable\Core\Enums\SortDirection;

class HttpTableStateResolver implements StateResolver
{
    public function resolve(): TableStateDTO
    {
        $direction = request()->input('direction', SortDirection::ASC->value);
        
        // Ensure we have a string value, not an array
        if (!is_string($direction)) {
            $direction = SortDirection::ASC->value;
        }

        if (! in_array($direction, SortDirection::getValues(), true)) {
            $direction = SortDirection::ASC->value;
        }

        $perPage = (int)request(
            'per_page',
            config('laravel-table.per_page', 15)
        );
        $perPage = min($perPage, config('laravel-table.max_per_page', 100));

        return new TableStateDTO(
            sort: request('sort'),
            direction: SortDirection::fromRequest($direction),
            filters: request('filter', []),
            search: request('search', null),
            page: (int)request('page', 1),
            perPage: $perPage,
        );
    }

}
