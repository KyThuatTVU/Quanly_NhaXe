<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Exceptions\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class CsrfMiddleware implements MiddlewareInterface
{
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function handle(Request $request, callable $next): Response
    {
        if (in_array($request->method(), self::SAFE_METHODS, true)) {
            return $next($request);
        }
        // Bearer token không tự gửi theo cookie nên không bị CSRF; auth qua cookie phiên thì vẫn phải có token.
        if ($request->attribute('auth_via') === 'bearer') {
            return $next($request);
        }

        $token = $request->header('x-csrf-token') ?? $request->input('_csrf');
        if (!Session::validateCsrf(is_string($token) ? $token : null)) {
            throw new HttpException('CSRF token không hợp lệ hoặc đã hết hạn', 403);
        }
        return $next($request);
    }
}
