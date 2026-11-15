<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Infrastructure\Persistence\Repositories\PdoSessionRepository;

final class WebAuthMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly PdoSessionRepository $sessions)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        $token = Session::get('auth_token');
        $token = is_string($token) ? $token : '';
        $session = $token !== '' ? $this->sessions->findActive($token) : null;

        if ($session === null) {
            Session::forget('auth_token');
            Session::set('_intended', $request->path());
            Session::set('_flash_next', array_merge(
                (array) Session::get('_flash_next', []),
                ['error' => 'Vui lòng đăng nhập để tiếp tục'],
            ));
            return Response::redirect(url('/admin/login'));
        }

        $request->setAttribute('auth', $session);
        $request->setAttribute('auth_token', $token);
        $request->setAttribute('auth_via', 'session');
        $this->sessions->touch($token);

        return $next($request);
    }
}
