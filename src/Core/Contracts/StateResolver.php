<?php

declare(strict_types=1);

namespace LaravelTable\Core\Contracts;

use LaravelTable\Core\DTO\TableStateDTO;

interface StateResolver
{
    public function resolve(): TableStateDTO;

}
