<?php

declare(strict_types=1);

use App\Core\Router;
use App\Http\Controllers\Web\AssetController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\CarController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\ReviewController;
use App\Http\Controllers\Web\ServiceController;
use App\Http\Controllers\Web\SiteController;
use App\Http\Middleware\CsrfMiddleware;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/gioi-thieu', [SiteController::class, 'about']);
    $router->get('/lien-he', [SiteController::class, 'contact']);
    $router->post('/lien-he', [SiteController::class, 'sendContact'], [CsrfMiddleware::class]);

    $router->get('/xe', [CarController::class, 'index']);
    $router->get('/xe/{id}', [CarController::class, 'show']);

    $router->get('/dich-vu', [ServiceController::class, 'index']);
    $router->get('/dich-vu/{id}', [ServiceController::class, 'show']);

    $router->get('/bai-viet', [PostController::class, 'index']);
    $router->get('/bai-viet/{slug}', [PostController::class, 'show']);

    $router->get('/dat-xe', [BookingController::class, 'index']);
    $router->post('/dat-xe', [BookingController::class, 'store'], [CsrfMiddleware::class]);
    $router->get('/dat-xe/thanh-cong', [BookingController::class, 'success']);

    $router->get('/tra-cuu-don', [BookingController::class, 'lookupForm']);
    $router->post('/tra-cuu-don', [BookingController::class, 'lookup'], [CsrfMiddleware::class]);

    $router->get('/danh-gia', [ReviewController::class, 'index']);
    $router->post('/danh-gia', [ReviewController::class, 'store'], [CsrfMiddleware::class]);

    $router->get('/assets/{file}', [AssetController::class, 'single']);
    $router->get('/assets/{dir}/{file}', [AssetController::class, 'nested']);
    $router->get('/assets/{dir}/{sub}/{file}', [AssetController::class, 'deep']);
};
