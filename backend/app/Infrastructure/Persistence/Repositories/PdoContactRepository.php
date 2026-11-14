<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoContactRepository extends Repository
{
    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO contacts (name, email, phone, subject, message, status)
             VALUES (:name, :email, :phone, :subject, :message, :status)',
            $data,
        );
    }

    public function paginate(array $filters, int $page, int $perPage): array
    {
        [$page, $offset] = $this->paging($page, $perPage);
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'c.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(c.name LIKE :q1 OR c.phone LIKE :q2 OR c.email LIKE :q3)';
            $like = '%' . $filters['search'] . '%';
            $params['q1'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);
        $total = (int) $this->scalar('SELECT COUNT(*) FROM contacts c' . $whereSql, $params);
        $items = $this->fetchAll(
            'SELECT c.*, u.username AS replied_by_name
             FROM contacts c
             LEFT JOIN users u ON u.user_id = c.replied_by' . $whereSql
            . ' ORDER BY c.contact_id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params,
        );

        return $this->paged($items, $total, $page, $perPage);
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT c.*, u.username AS replied_by_name
             FROM contacts c LEFT JOIN users u ON u.user_id = c.replied_by
             WHERE c.contact_id = :id',
            ['id' => $id],
        );
    }

    public function updateStatus(int $id, string $status, ?int $userId): void
    {
        if ($status === 'replied') {
            $this->write(
                'UPDATE contacts SET status = :status, replied_by = :user_id, replied_at = :replied_at
                 WHERE contact_id = :id',
                [
                    'status' => $status,
                    'user_id' => $userId,
                    'replied_at' => date('Y-m-d H:i:s'),
                    'id' => $id,
                ],
            );
            return;
        }
        $this->write('UPDATE contacts SET status = :status WHERE contact_id = :id', [
            'status' => $status,
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->write('DELETE FROM contacts WHERE contact_id = :id', ['id' => $id]);
    }

    public function countNew(): int
    {
        return (int) $this->scalar("SELECT COUNT(*) FROM contacts WHERE status = 'new'");
    }
}
