<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'driver' => 'mysql',
    'host' => Config::env('DB_HOST', '127.0.0.1'),
    'port' => (int) Config::env('DB_PORT', '3306'),
    'database' => Config::env('DB_DATABASE', 'thaovy_xe_hop_dong'),
    'username' => Config::env('DB_USERNAME', 'root'),
    'password' => Config::env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'timezone' => '+07:00',
];
