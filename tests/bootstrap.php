<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

if (! function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return __DIR__ . '/../config' . ($path !== '' ? '/' . $path : '');
    }
}
