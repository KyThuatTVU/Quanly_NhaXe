<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Config;
use App\Core\Response;
use App\Infrastructure\Persistence\Connection;

final class HealthController extends ApiController
{
    public function index(): Response
    {
        try {
            $ok = Connection::pdo()->query('SELECT 1')->fetchColumn() !== false;
        } catch (\Throwable) {
            return Response::fail('Không kết nối được cơ sở dữ liệu', 503);
        }

        return Response::ok([
            'app' => (string) Config::get('app.name'),
            'env' => (string) Config::get('app.env'),
            'php' => PHP_VERSION,
            'database' => $ok ? 'ok' : 'error',
            'time' => date('c'),
        ]);
    }
}
