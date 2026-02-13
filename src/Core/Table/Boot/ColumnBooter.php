<?php

declare(strict_types=1);

namespace LaravelTable\Core\Table\Boot;

use LaravelTable\Core\Columns\Registry\ColumnRegistry;

class ColumnBooter
{
    public function boot(ColumnRegistry $registry): void
    {
        $registry->boot();
    }

}
