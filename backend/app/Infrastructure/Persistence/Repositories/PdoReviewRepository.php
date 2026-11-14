<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoReviewRepository extends Repository
{
    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO ratings (booking_id, vehicle_id, customer_id, rating, comment, is_visible)
             VALUES (:booking_id, :vehicle_id, :customer_id, :rating, :comment, :is_visible)',
            $data,
        );
    }

    public function byVehicle(int $vehicleId, bool $visibleOnly = true): array
    {
        $sql = 'SELECT r.*, u.username
                FROM ratings r
                LEFT JOIN users u ON u.user_id = r.customer_id
                WHERE r.vehicle_id = :vehicle_id';
        if ($visibleOnly) {
            $sql .= ' AND r.is_visible = 1';
        }
        return $this->fetchAll($sql . ' ORDER BY r.rating_id DESC', ['vehicle_id' => $vehicleId]);
    }

    public function paginate(?int $visible, int $page, int $perPage): array
    {
        [$page, $offset] = $this->paging($page, $perPage);
        $where = '';
        $params = [];
        if ($visible !== null) {
            $where = ' WHERE r.is_visible = :visible';
            $params['visible'] = $visible;
        }

        $total = (int) $this->scalar('SELECT COUNT(*) FROM ratings r' . $where, $params);
        $items = $this->fetchAll(
            'SELECT r.*, v.name AS vehicle_name, u.username
             FROM ratings r
             LEFT JOIN vehicles v ON v.vehicle_id = r.vehicle_id
             LEFT JOIN users u ON u.user_id = r.customer_id'
            . $where . ' ORDER BY r.rating_id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params,
        );

        return $this->paged($items, $total, $page, $perPage);
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM ratings WHERE rating_id = :id', ['id' => $id]);
    }

    public function setVisible(int $id, bool $visible): void
    {
        $this->write('UPDATE ratings SET is_visible = :visible WHERE rating_id = :id', [
            'visible' => $visible ? 1 : 0,
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->write('DELETE FROM ratings WHERE rating_id = :id', ['id' => $id]);
    }

    public function existsForBooking(int $bookingId): bool
    {
        return (int) $this->scalar('SELECT COUNT(*) FROM ratings WHERE booking_id = :id', ['id' => $bookingId]) > 0;
    }

    public function average(int $vehicleId): float
    {
        return (float) $this->scalar(
            'SELECT COALESCE(AVG(rating), 0) FROM ratings WHERE vehicle_id = :id AND is_visible = 1',
            ['id' => $vehicleId],
        );
    }

    public function countAll(): int
    {
        return (int) $this->scalar('SELECT COUNT(*) FROM ratings');
    }
}
