<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoSessionRepository extends Repository
{
    public function create(string $id, int $userId, string $ip, string $userAgent, string $expiresAt): void
    {
        $this->write(
            'INSERT INTO admin_sessions (session_id, user_id, ip_address, user_agent, is_active, expires_at)
             VALUES (:id, :user_id, :ip, :agent, 1, :expires_at)',
            [
                'id' => $id,
                'user_id' => $userId,
                'ip' => $ip,
                'agent' => mb_substr($userAgent, 0, 512),
                'expires_at' => $expiresAt,
            ],
        );
    }

    public function findActive(string $id): ?array
    {
        return $this->fetchOne(
            'SELECT s.session_id, s.expires_at, u.user_id, u.username, u.email, u.phone, u.role, u.is_active
             FROM admin_sessions s
             INNER JOIN users u ON u.user_id = s.user_id
             WHERE s.session_id = :id AND s.is_active = 1 AND s.expires_at > NOW()',
            ['id' => $id],
        );
    }

    public function touch(string $id): void
    {
        $this->write(
            'UPDATE admin_sessions SET last_activity = NOW() WHERE session_id = :id',
            ['id' => $id],
        );
    }

    public function deactivate(string $id): void
    {
        $this->write(
            'UPDATE admin_sessions SET is_active = 0 WHERE session_id = :id',
            ['id' => $id],
        );
    }

    public function deactivateAllForUser(int $userId): void
    {
        $this->write(
            'UPDATE admin_sessions SET is_active = 0 WHERE user_id = :id AND is_active = 1',
            ['id' => $userId],
        );
    }

    public function deleteExpired(): void
    {
        $this->write('DELETE FROM admin_sessions WHERE expires_at <= NOW() OR is_active = 0');
    }
}
