<?php

declare(strict_types=1);

use App\Core\Router;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\CarTypeController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Middleware\CsrfMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\WebAuthMiddleware;

return static function (Router $router): void {
    $router->get('/admin/login', [AuthController::class, 'showLogin']);
    $router->post('/admin/login', [AuthController::class, 'login'], [CsrfMiddleware::class]);
    $router->post('/admin/logout', [AuthController::class, 'logout'], [WebAuthMiddleware::class]);

    $router->group(
        '/admin',
        [WebAuthMiddleware::class, [RoleMiddleware::class, ['admin', 'business']], CsrfMiddleware::class],
        static function (Router $router): void {
            $router->get('', [DashboardController::class, 'index']);

            $router->get('/bookings', [BookingController::class, 'index']);
            $router->get('/bookings/{id}', [BookingController::class, 'show']);
            $router->patch('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
            $router->delete('/bookings/{id}', [BookingController::class, 'destroy']);

            $router->get('/cars', [CarController::class, 'index']);
            $router->get('/cars/create', [CarController::class, 'create']);
            $router->post('/cars', [CarController::class, 'store']);
            $router->get('/cars/{id}/edit', [CarController::class, 'edit']);
            $router->put('/cars/{id}', [CarController::class, 'update']);
            $router->delete('/cars/{id}', [CarController::class, 'destroy']);

            $router->get('/car-types', [CarTypeController::class, 'index']);
            $router->get('/car-types/create', [CarTypeController::class, 'create']);
            $router->post('/car-types', [CarTypeController::class, 'store']);
            $router->get('/car-types/{id}/edit', [CarTypeController::class, 'edit']);
            $router->put('/car-types/{id}', [CarTypeController::class, 'update']);
            $router->delete('/car-types/{id}', [CarTypeController::class, 'destroy']);

            $router->get('/services', [ServiceController::class, 'index']);
            $router->get('/services/create', [ServiceController::class, 'create']);
            $router->post('/services', [ServiceController::class, 'store']);
            $router->get('/services/{id}/edit', [ServiceController::class, 'edit']);
            $router->put('/services/{id}', [ServiceController::class, 'update']);
            $router->delete('/services/{id}', [ServiceController::class, 'destroy']);

            $router->get('/posts', [PostController::class, 'index']);
            $router->get('/posts/create', [PostController::class, 'create']);
            $router->post('/posts', [PostController::class, 'store']);
            $router->get('/posts/{id}/edit', [PostController::class, 'edit']);
            $router->put('/posts/{id}', [PostController::class, 'update']);
            $router->delete('/posts/{id}', [PostController::class, 'destroy']);

            $router->get('/contacts', [ContactController::class, 'index']);
            $router->get('/contacts/{id}', [ContactController::class, 'show']);
            $router->patch('/contacts/{id}/status', [ContactController::class, 'updateStatus']);
            $router->delete('/contacts/{id}', [ContactController::class, 'destroy']);

            $router->get('/reviews', [ReviewController::class, 'index']);
            $router->patch('/reviews/{id}/visible', [ReviewController::class, 'setVisible']);
            $router->delete('/reviews/{id}', [ReviewController::class, 'destroy']);

            $router->get('/settings', [SettingController::class, 'index']);
            $router->put('/settings', [SettingController::class, 'update']);

            $router->get('/profile', [ProfileController::class, 'index']);
            $router->put('/profile/password', [ProfileController::class, 'updatePassword']);

            $router->get('/accounts', [AccountController::class, 'index'], [[RoleMiddleware::class, ['admin']]]);
            $router->get('/accounts/create', [AccountController::class, 'create'], [[RoleMiddleware::class, ['admin']]]);
            $router->post('/accounts', [AccountController::class, 'store'], [[RoleMiddleware::class, ['admin']]]);
            $router->get('/accounts/{id}/edit', [AccountController::class, 'edit'], [[RoleMiddleware::class, ['admin']]]);
            $router->put('/accounts/{id}', [AccountController::class, 'update'], [[RoleMiddleware::class, ['admin']]]);
            $router->delete('/accounts/{id}', [AccountController::class, 'destroy'], [[RoleMiddleware::class, ['admin']]]);

            $router->get('/audit-logs', [AuditLogController::class, 'index'], [[RoleMiddleware::class, ['admin']]]);
        },
    );
};
