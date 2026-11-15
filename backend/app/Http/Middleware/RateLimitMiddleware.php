<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Infrastructure\Persistence\Connection;
use Throwable;

/**
 * Giới hạn tần suất theo IP + phạm vi (fixed window, lưu trong MySQL).
 * Lỗi CSDL thì bỏ qua giới hạn để không làm sập website.
 */
final class RateLimitMiddleware implements MiddlewareInterface
{
    private string $scope = 'default';
    private int $max = 30;
    private int $window = 60;

    /**
     * @param array<int, mixed> $params [scope, max, windowSeconds]
     */
    public function setParams(array $params): void
    {
        $this->scope = (string) ($params[0] ?? 'default');
        $this->max = max(1, (int) ($params[1] ?? 30));
        $this->window = max(1, (int) ($params[2] ?? 60));
    }

    public function handle(Request $request, callable $next): Response
    {
        $bucket = hash('sha256', $this->scope . '|' . $request->ip());

        try {
            [$hits, $retryAfter] = $this->hit($bucket);
        } catch (Throwable) {
            return $next($request);
        }

        if ($hits <= $this->max) {
            return $next($request);
        }

        $message = 'Bạn thao tác quá nhanh. Vui lòng thử lại sau ' . $retryAfter . ' giây.';
        $response = str_starts_with($request->path(), '/api')
            ? Response::fail($message, 429)
            : View::renderResponse('errors/429', [
                'status' => 429,
                'message' => $message,
                'site' => ['name' => (string) Config::get('app.name', 'Nhà xe')],
            ], null)->withStatus(429);

        return $response
            ->withHeader('Retry-After', (string) $retryAfter)
            ->withHeader('X-RateLimit-Limit', (string) $this->max)
            ->withHeader('X-RateLimit-Remaining', '0');
    }

    /**
     * @return array{0: int, 1: int} [số lần đã dùng trong cửa sổ, số giây còn lại]
     */
    private function hit(string $bucket): array
    {
        $pdo = Connection::pdo();
        $statement = $pdo->prepare(
            'INSERT INTO rate_limits (bucket_key, scope, hits, reset_at)
             VALUES (:bucket, :scope, 1, DATE_ADD(NOW(), INTERVAL :window1 SECOND))
             ON DUPLICATE KEY UPDATE
                 hits = IF(reset_at <= NOW(), 1, hits + 1),
                 reset_at = IF(reset_at <= NOW(), DATE_ADD(NOW(), INTERVAL :window2 SECOND), reset_at)',
        );
        $statement->execute([
            'bucket' => $bucket,
            'scope' => $this->scope,
            'window1' => $this->window,
            'window2' => $this->window,
        ]);

        if ($statement->rowCount() === 1 && random_int(1, 50) === 1) {
            try {
                $pdo->exec('DELETE FROM rate_limits WHERE reset_at < DATE_SUB(NOW(), INTERVAL 1 DAY)');
            } catch (Throwable) {
                // Dọn dẹp chỉ là tối ưu, không ảnh hưởng việc đếm.
            }
        }

        $lookup = $pdo->prepare(
            'SELECT hits, GREATEST(1, TIMESTAMPDIFF(SECOND, NOW(), reset_at)) AS retry_after
             FROM rate_limits WHERE bucket_key = :bucket',
        );
        $lookup->execute(['bucket' => $bucket]);
        $row = $lookup->fetch();

        return [(int) ($row['hits'] ?? 0), (int) ($row['retry_after'] ?? $this->window)];
    }
}
