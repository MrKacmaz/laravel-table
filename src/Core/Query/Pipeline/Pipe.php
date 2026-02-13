<?php

namespace LaravelTable\Core\Query\Pipeline;

use Closure;

interface Pipe
{
    public function handle(mixed $passable, Closure $next): mixed;

}
