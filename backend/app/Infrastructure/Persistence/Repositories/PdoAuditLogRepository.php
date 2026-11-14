<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoAuditLogRepository extends Repository
{
    public function add(
        ?int $userId,
        string $action,
        ?string $entityType,
        ?int $entityId,
        ?string $description,
        string $ip,
        string $userAgent,
    ): void {
        $this->write(
            'INSERT INTO audit_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent)
             VALUES (:user_id, :action, :entity_type, :entity_id, :description, :ip, :agent)',
            [
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'ip' => $ip,
                'agent' => mb_substr($userAgent, 0, 512),
            ],
        );
    }

    public function paginate(int $page, int $perPage): array
    {
        [$page, $offset] = $this->paging($page, $perPage);
        $total = (int) $this->scalar('SELECT COUNT(*) FROM audit_logs');
        $items = $this->fetchAll(
            'SELECT a.*, u.username FROM audit_logs a
             LEFT JOIN users u ON u.user_id = a.user_id
             ORDER BY a.log_id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset,
        );
        return $this->paged($items, $total, $page, $perPage);
    }
}
