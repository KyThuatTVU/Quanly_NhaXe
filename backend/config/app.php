<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'name' => Config::env('APP_NAME', 'Nhà xe'),
    'env' => Config::env('APP_ENV', 'local'),
    'debug' => filter_var(Config::env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOL),
    'url' => rtrim((string) Config::env('APP_URL', 'http://127.0.0.1'), '/'),
    'timezone' => Config::env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'),
    'base_path' => dirname(__DIR__),
];
