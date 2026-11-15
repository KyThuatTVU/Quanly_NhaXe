<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Router;

$basePath = dirname(__DIR__);

require $basePath . '/bootstrap/autoload.php';

Config::bootstrap($basePath);

require $basePath . '/app/Support/helpers.php';

error_reporting(E_ALL);
ini_set('display_errors', '0');

$router = new Router();
(require $basePath . '/routes/web.php')($router);
(require $basePath . '/routes/admin.php')($router);
(require $basePath . '/routes/api.php')($router);

return $router;
