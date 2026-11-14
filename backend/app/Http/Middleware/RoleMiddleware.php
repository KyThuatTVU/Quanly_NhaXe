<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Request;
use App\Core\Response;

final class RoleMiddleware implements MiddlewareInterface
{
    /** @var array<int, string> */
    private array $roles = [];

    /**
     * @param array<int, string> $roles
     */
    public function setParams(array $roles): void
    {
        $this->roles = $roles;
    }

    public function handle(Request $request, callable $next): Response
    {
        $user = $request->user();
        if ($user === null) {
            throw new UnauthorizedException();
        }
        if ($this->roles !== [] && !in_array((string) $user['role'], $this->roles, true)) {
            throw new ForbiddenException('Bạn không có quyền truy cập chức năng này');
        }
        return $next($request);
    }
}
