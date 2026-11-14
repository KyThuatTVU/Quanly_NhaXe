<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Router;

$basePath = dirname(__DIR__);

spl_autoload_register(static function (string $class) use ($basePath): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = $basePath . '/app/' . $relative . '.php';
    if (is_file($file)) {
        require $file;
    }
});

Config::bootstrap($basePath);

error_reporting(E_ALL);
ini_set('display_errors', '0');

$router = new Router();
(require $basePath . '/routes/api.php')($router);

return $router;
