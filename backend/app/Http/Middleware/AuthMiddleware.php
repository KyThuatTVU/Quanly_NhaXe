<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Exceptions\UnauthorizedException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Infrastructure\Persistence\Repositories\PdoSessionRepository;

final class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly PdoSessionRepository $sessions)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        $token = $this->token($request);
        if ($token === null || $token === '') {
            throw new UnauthorizedException('Vui lòng đăng nhập để tiếp tục');
        }

        $session = $this->sessions->findActive($token);
        if ($session === null) {
            throw new UnauthorizedException('Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại');
        }
        if ((int) $session['is_active'] !== 1) {
            throw new UnauthorizedException('Tài khoản đã bị khóa');
        }

        $request->setAttribute('auth', $session);
        $request->setAttribute('auth_token', $token);
        $this->sessions->touch($token);

        return $next($request);
    }

    private function token(Request $request): ?string
    {
        $header = (string) $request->header('authorization', '');
        if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches) === 1) {
            return trim($matches[1]);
        }
        $token = $request->header('x-auth-token');
        if ($token !== null && $token !== '') {
            return $token;
        }
        $sessionToken = Session::get('auth_token');
        return is_string($sessionToken) ? $sessionToken : null;
    }
}
