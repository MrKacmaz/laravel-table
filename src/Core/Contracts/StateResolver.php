<?php

namespace LaravelTable\Core\Contracts;

use LaravelTable\Core\DTO\TableStateDTO;

interface StateResolver
{
    public function resolve(): TableStateDTO;

}
