<?php

declare(strict_types=1);

use App\Core\Request;
use App\Core\Response;
use App\Http\Middleware\RateLimitMiddleware;
use App\Infrastructure\Persistence\Connection;

function rate_limit_api_request(string $ip): Request
{
    return new Request('POST', '/api/contacts', [], [], [], ['content-type' => 'application/json'], '', $ip);
}

function rate_limit_web_request(string $path, string $ip): Request
{
    return new Request('POST', $path, [], [], [], [], '', $ip);
}

return [
    'API trả 429 sau khi vượt ngưỡng' => static function (): void {
        Connection::pdo()->exec('DELETE FROM rate_limits');

        $limiter = new RateLimitMiddleware();
        $limiter->setParams(['test_api', 2, 60]);
        $next = static fn (Request $request): Response => Response::ok(['ok' => true]);

        assert_same(200, $limiter->handle(rate_limit_api_request('198.51.100.7'), $next)->status());
        assert_same(200, $limiter->handle(rate_limit_api_request('198.51.100.7'), $next)->status());

        $blocked = $limiter->handle(rate_limit_api_request('198.51.100.7'), $next);
        assert_same(429, $blocked->status());
        assert_contains('quá nhanh', $blocked->body());
    },

    'Mỗi IP có bộ đếm riêng' => static function (): void {
        Connection::pdo()->exec('DELETE FROM rate_limits');

        $limiter = new RateLimitMiddleware();
        $limiter->setParams(['test_ip', 1, 60]);
        $next = static fn (Request $request): Response => Response::ok(['ok' => true]);

        assert_same(200, $limiter->handle(rate_limit_api_request('198.51.100.8'), $next)->status());
        assert_same(429, $limiter->handle(rate_limit_api_request('198.51.100.8'), $next)->status());
        assert_same(200, $limiter->handle(rate_limit_api_request('198.51.100.9'), $next)->status());
    },

    'Form web hiển thị trang 429 thay vì JSON' => static function (): void {
        Connection::pdo()->exec('DELETE FROM rate_limits');

        $limiter = new RateLimitMiddleware();
        $limiter->setParams(['test_web', 1, 60]);
        $next = static fn (Request $request): Response => Response::ok(['ok' => true]);

        assert_same(200, $limiter->handle(rate_limit_web_request('/lien-he', '198.51.100.10'), $next)->status());

        $blocked = $limiter->handle(rate_limit_web_request('/lien-he', '198.51.100.10'), $next);
        assert_same(429, $blocked->status());
        assert_contains('Thao tác quá nhanh', $blocked->body());
    },

    'Hết cửa sổ thời gian thì đếm lại từ đầu' => static function (): void {
        Connection::pdo()->exec('DELETE FROM rate_limits');

        $limiter = new RateLimitMiddleware();
        $limiter->setParams(['test_window', 1, 60]);
        $next = static fn (Request $request): Response => Response::ok(['ok' => true]);

        assert_same(200, $limiter->handle(rate_limit_api_request('198.51.100.11'), $next)->status());
        assert_same(429, $limiter->handle(rate_limit_api_request('198.51.100.11'), $next)->status());

        // Giả lập cửa sổ đã hết hạn thay vì chờ 60 giây.
        Connection::pdo()->exec('UPDATE rate_limits SET reset_at = DATE_SUB(NOW(), INTERVAL 1 SECOND)');

        assert_same(200, $limiter->handle(rate_limit_api_request('198.51.100.11'), $next)->status());
    },
];
