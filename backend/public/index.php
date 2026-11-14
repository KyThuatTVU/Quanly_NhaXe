<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Exceptions\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Domain\Exceptions\BusinessRuleException;

/** @var \App\Core\Router $router */
$router = require dirname(__DIR__) . '/bootstrap/app.php';

$request = Request::capture();

try {
    $response = $router->dispatch($request);
} catch (HttpException $exception) {
    $response = Response::fail($exception->getMessage(), $exception->statusCode(), $exception->errors());
} catch (BusinessRuleException $exception) {
    $response = Response::fail($exception->getMessage(), 409);
} catch (Throwable $exception) {
    $logDir = dirname(__DIR__) . '/storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }
    @file_put_contents(
        $logDir . '/app-' . date('Y-m-d') . '.log',
        sprintf(
            "[%s] %s %s | %s in %s:%d\n%s\n",
            date('Y-m-d H:i:s'),
            $request->method(),
            $request->path(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString(),
        ),
        FILE_APPEND,
    );

    $message = (bool) Config::get('app.debug', false)
        ? $exception->getMessage() . ' (' . basename($exception->getFile()) . ':' . $exception->getLine() . ')'
        : 'Đã xảy ra lỗi hệ thống, vui lòng thử lại sau';
    $response = Response::fail($message, 500);
}

$response
    ->withHeader('X-Content-Type-Options', 'nosniff')
    ->withHeader('X-Frame-Options', 'SAMEORIGIN')
    ->withHeader('Referrer-Policy', 'same-origin')
    ->send();
