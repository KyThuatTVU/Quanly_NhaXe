<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Exceptions\HttpException;
use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Domain\Exceptions\BusinessRuleException;
use App\Support\Flash;

// Truy cập qua URL cũ /backend/public/...: chuyển hẳn (301) sang URL sạch
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$legacySuffix = '/backend/public/index.php';
if (str_ends_with($scriptName, $legacySuffix)) {
    $projectBase = substr($scriptName, 0, -strlen($legacySuffix));
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $legacyPrefix = $projectBase . '/backend/public';
    if (str_starts_with($uri, $legacyPrefix)) {
        $legacyRoute = trim(rawurldecode('backend/public' . substr($uri, strlen($legacyPrefix))), '/');
        require_once dirname(__DIR__) . '/bootstrap/legacy-redirect.php';
        $legacyTarget = legacy_backend_target($legacyRoute) ?? 'home/';

        $query = (string) ($_SERVER['QUERY_STRING'] ?? '');
        if ($query !== '') {
            $legacyTarget .= (str_contains($legacyTarget, '?') ? '&' : '?') . $query;
        }

        header('Location: ' . $projectBase . '/' . $legacyTarget, true, 301);
        exit;
    }
}

/** @var \App\Core\Router $router */
$router = require dirname(__DIR__) . '/bootstrap/app.php';

$request = Request::capture();
$path = $request->path();
$isWeb = !str_starts_with($path, '/api') && !str_starts_with($path, '/uploads') && !str_starts_with($path, '/assets');

$redirectBack = static function (Request $request) use ($path): Response {
    $referer = (string) $request->header('referer', '');
    if ($referer !== '' && str_starts_with($referer, base_url())) {
        return Response::redirect($referer);
    }
    return Response::redirect(url(str_starts_with($path, '/admin') ? '/admin' : '/'));
};

$errorPage = static function (int $status, string $message) {
    $view = match ($status) {
        403 => 'errors/403',
        404 => 'errors/404',
        default => 'errors/500',
    };
    return View::renderResponse($view, [
        'status' => $status,
        'message' => $message,
        'site' => ['name' => (string) Config::get('app.name', 'Nhà xe')],
    ], null)->withStatus($status);
};

try {
    $response = $router->dispatch($request);
} catch (ValidationException $exception) {
    if ($isWeb) {
        Flash::put('errors', $exception->errors());
        Flash::put('old', $request->all());
        $response = $redirectBack($request);
    } else {
        $response = Response::fail($exception->getMessage(), 422, $exception->errors());
    }
} catch (BusinessRuleException $exception) {
    if ($isWeb) {
        Flash::put('error', $exception->getMessage());
        Flash::put('old', $request->all());
        $response = $redirectBack($request);
    } else {
        $response = Response::fail($exception->getMessage(), 409);
    }
} catch (HttpException $exception) {
    if ($isWeb && $exception->statusCode() === 401) {
        $response = Response::redirect(url('/admin/login'));
    } elseif ($isWeb) {
        $response = $errorPage($exception->statusCode(), $exception->getMessage());
    } else {
        $response = Response::fail($exception->getMessage(), $exception->statusCode(), $exception->errors());
    }
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
    $response = $isWeb ? $errorPage(500, $message) : Response::fail($message, 500);
}

$response
    ->withHeader('X-Content-Type-Options', 'nosniff')
    ->withHeader('X-Frame-Options', 'SAMEORIGIN')
    ->withHeader('Referrer-Policy', 'same-origin')
    ->send();
