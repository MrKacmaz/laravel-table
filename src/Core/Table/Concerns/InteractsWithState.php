<?php

declare(strict_types=1);

namespace LaravelTable\Core\Table\Concerns;

use LaravelTable\Core\DTO\TableStateDTO;

trait InteractsWithState
{
    public function setState(TableStateDTO $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getState(): TableStateDTO
    {
        return $this->state;
    }

}
