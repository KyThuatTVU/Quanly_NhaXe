<?php

declare(strict_types=1);

use App\Core\Router;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CarTypeController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ServiceCatalogController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StatisticController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\CsrfMiddleware;
use App\Http\Middleware\RoleMiddleware;

return static function (Router $router): void {
    // ------------------------------------------------------------------
    // Công khai (website khách)
    // ------------------------------------------------------------------
    $router->get('/', [HealthController::class, 'index']);
    $router->get('/api/health', [HealthController::class, 'index']);
    $router->get('/api/auth/csrf', [AuthController::class, 'csrf']);
    $router->post('/api/auth/login', [AuthController::class, 'login'], [CsrfMiddleware::class]);

    $router->get('/api/settings', [SettingController::class, 'show']);

    $router->get('/api/vehicle-types', [CarTypeController::class, 'index']);
    $router->get('/api/vehicle-types/{id}', [CarTypeController::class, 'show']);

    $router->get('/api/vehicles', [CarController::class, 'index']);
    $router->get('/api/vehicles/{id}/reviews', [ReviewController::class, 'byVehicle']);
    $router->get('/api/vehicles/{id}', [CarController::class, 'show']);

    $router->get('/api/services', [ServiceCatalogController::class, 'index']);
    $router->get('/api/services/{id}', [ServiceCatalogController::class, 'show']);

    $router->get('/api/posts/latest', [PostController::class, 'latest']);
    $router->get('/api/posts/categories', [PostController::class, 'categories']);
    $router->get('/api/posts', [PostController::class, 'index']);
    $router->get('/api/posts/{slug}', [PostController::class, 'show']);

    $router->get('/api/bookings/availability', [BookingController::class, 'checkAvailability']);
    $router->get('/api/bookings/lookup', [BookingController::class, 'lookup']);
    $router->post('/api/bookings', [BookingController::class, 'store'], [CsrfMiddleware::class]);

    $router->post('/api/contacts', [ContactController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/api/reviews', [ReviewController::class, 'store'], [CsrfMiddleware::class]);

    $router->get('/uploads/{folder}/{filename}', [MediaController::class, 'show']);

    // ------------------------------------------------------------------
    // Quản trị (Bearer token + phân quyền)
    // ------------------------------------------------------------------
    $router->group('/api/admin', [AuthMiddleware::class, [RoleMiddleware::class, ['admin', 'business']]], static function (Router $router): void {
        $router->get('/auth/me', [AuthController::class, 'me']);
        $router->post('/auth/logout', [AuthController::class, 'logout']);
        $router->post('/auth/change-password', [AuthController::class, 'changePassword']);

        $router->get('/dashboard', [StatisticController::class, 'dashboard']);
        $router->get('/statistics/grouped', [StatisticController::class, 'grouped']);
        $router->get('/statistics/summary', [StatisticController::class, 'summary']);

        $router->get('/bookings', [BookingController::class, 'adminIndex']);
        $router->get('/bookings/{id}', [BookingController::class, 'adminShow']);
        $router->patch('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
        $router->delete('/bookings/{id}', [BookingController::class, 'destroy']);

        $router->get('/vehicles', [CarController::class, 'adminIndex']);
        $router->post('/vehicles', [CarController::class, 'store']);
        $router->put('/vehicles/{id}', [CarController::class, 'update']);
        $router->delete('/vehicles/{id}', [CarController::class, 'destroy']);

        $router->get('/vehicle-types', [CarTypeController::class, 'adminIndex']);
        $router->post('/vehicle-types', [CarTypeController::class, 'store']);
        $router->put('/vehicle-types/{id}', [CarTypeController::class, 'update']);
        $router->delete('/vehicle-types/{id}', [CarTypeController::class, 'destroy']);

        $router->get('/services', [ServiceCatalogController::class, 'adminIndex']);
        $router->post('/services', [ServiceCatalogController::class, 'store']);
        $router->put('/services/{id}', [ServiceCatalogController::class, 'update']);
        $router->delete('/services/{id}', [ServiceCatalogController::class, 'destroy']);

        $router->get('/posts', [PostController::class, 'adminIndex']);
        $router->get('/posts/{id}', [PostController::class, 'adminShow']);
        $router->post('/posts', [PostController::class, 'store']);
        $router->put('/posts/{id}', [PostController::class, 'update']);
        $router->delete('/posts/{id}', [PostController::class, 'destroy']);

        $router->get('/contacts', [ContactController::class, 'adminIndex']);
        $router->get('/contacts/{id}', [ContactController::class, 'adminShow']);
        $router->patch('/contacts/{id}/status', [ContactController::class, 'updateStatus']);
        $router->delete('/contacts/{id}', [ContactController::class, 'destroy']);

        $router->get('/reviews', [ReviewController::class, 'adminIndex']);
        $router->patch('/reviews/{id}/visible', [ReviewController::class, 'setVisible']);
        $router->delete('/reviews/{id}', [ReviewController::class, 'destroy']);

        $router->put('/settings', [SettingController::class, 'update']);

        $router->post('/uploads', [UploadController::class, 'store']);

        $router->get('/accounts', [AccountController::class, 'index'], [[RoleMiddleware::class, ['admin']]]);
        $router->get('/accounts/{id}', [AccountController::class, 'show'], [[RoleMiddleware::class, ['admin']]]);
        $router->post('/accounts', [AccountController::class, 'store'], [[RoleMiddleware::class, ['admin']]]);
        $router->put('/accounts/{id}', [AccountController::class, 'update'], [[RoleMiddleware::class, ['admin']]]);
        $router->delete('/accounts/{id}', [AccountController::class, 'destroy'], [[RoleMiddleware::class, ['admin']]]);

        $router->get('/audit-logs', [AuditLogController::class, 'index'], [[RoleMiddleware::class, ['admin']]]);
    });
};
