<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Config;
use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Request;
use App\Domain\Exceptions\BusinessRuleException;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Repositories\PdoSessionRepository;
use App\Infrastructure\Persistence\Repositories\PdoUserRepository;
use App\Support\Str;

final class AuthService
{
    public function __construct(
        private readonly PdoUserRepository $users,
        private readonly PdoSessionRepository $sessions,
        private readonly AuditLogger $audit,
    ) {
    }

    /**
     * @return array{token: string, expires_at: string, user: array<string, mixed>}
     */
    public function login(string $login, string $password, Request $request): array
    {
        $user = $this->users->findByLogin($login);
        if ($user === null || empty($user['password']) || !password_verify($password, (string) $user['password'])) {
            $this->audit->log($request, 'login_failed', 'users', null, 'Sai thông tin đăng nhập: ' . $login);
            throw new UnauthorizedException('Tên đăng nhập hoặc mật khẩu không đúng');
        }

        return $this->issueSession($user, $request, 'Đăng nhập quản trị');
    }

    /**
     * Đăng nhập quản trị bằng email đã xác thực từ Google (không cần mật khẩu).
     */
    public function loginWithGoogleEmail(string $email, Request $request): array
    {
        $user = $this->users->findByLogin($email);
        if ($user === null || strcasecmp((string) ($user['email'] ?? ''), $email) !== 0) {
            $this->audit->log($request, 'login_failed', 'users', null, 'Google: email chưa được cấp quyền: ' . $email);
            throw new UnauthorizedException('Email Google này chưa được cấp quyền truy cập trang quản trị');
        }

        return $this->issueSession($user, $request, 'Đăng nhập quản trị qua Google');
    }

    /**
     * @param array<string, mixed> $user
     * @return array{token: string, expires_at: string, user: array<string, mixed>}
     */
    private function issueSession(array $user, Request $request, string $auditMessage): array
    {
        if ((int) $user['is_active'] !== 1) {
            throw new ForbiddenException('Tài khoản đã bị khóa, vui lòng liên hệ quản trị viên');
        }
        if (!in_array($user['role'], (array) Config::get('auth.admin_roles', []), true)) {
            throw new ForbiddenException('Tài khoản này không có quyền truy cập trang quản trị');
        }

        $lifetime = (int) Config::get('auth.session_lifetime', 1440);
        $token = Str::token(32);
        $expiresAt = date('Y-m-d H:i:s', time() + $lifetime * 60);

        $this->sessions->deleteExpired();
        $this->sessions->create($token, (int) $user['user_id'], $request->ip(), (string) $request->header('user-agent', ''), $expiresAt);
        $this->users->touchLogin((int) $user['user_id']);
        $user = $this->users->find((int) $user['user_id']) ?? $user;

        $request->setAttribute('auth', $user);
        $this->audit->log($request, 'login', 'users', (int) $user['user_id'], $auditMessage);

        return [
            'token' => $token,
            'expires_at' => $expiresAt,
            'user' => $this->publicUser($user),
        ];
    }

    public function logout(?string $token, Request $request): void
    {
        if ($token !== null && $token !== '') {
            $this->sessions->deactivate($token);
        }
        $this->audit->log($request, 'logout', 'users', $request->userId(), 'Đăng xuất');
    }

    public function changePassword(array $user, string $currentPassword, string $newPassword): void
    {
        $fresh = $this->users->findByLogin((string) $user['username']);
        if ($fresh === null) {
            throw new BusinessRuleException('Không tìm thấy tài khoản');
        }
        if (empty($fresh['password']) || !password_verify($currentPassword, (string) $fresh['password'])) {
            throw new BusinessRuleException('Mật khẩu hiện tại không đúng');
        }
        $min = (int) Config::get('auth.password_min_length', 8);
        if (mb_strlen($newPassword) < $min) {
            throw new BusinessRuleException("Mật khẩu mới phải có ít nhất {$min} ký tự");
        }

        $this->users->updatePassword((int) $user['user_id'], password_hash($newPassword, PASSWORD_DEFAULT));
    }

    /**
     * @return array<string, mixed>
     */
    public function publicUser(array $user): array
    {
        return [
            'user_id' => (int) $user['user_id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
            'is_active' => (int) ($user['is_active'] ?? 1),
            'last_login_at' => $user['last_login_at'] ?? null,
        ];
    }
}
