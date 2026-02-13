<?php

namespace LaravelTable\Core\Table\Boot;

use LaravelTable\Core\Table\Table;

class TableBooter
{
    public function boot(Table $table): void
    {
        $table->boot();
    }

}
