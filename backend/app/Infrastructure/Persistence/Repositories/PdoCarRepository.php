<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoCarRepository extends Repository
{
    public function all(array $filters = []): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['vehicle_type_id'])) {
            $where[] = 'v.vehicle_type_id = :vehicle_type_id';
            $params['vehicle_type_id'] = (int) $filters['vehicle_type_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'v.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(v.name LIKE :q1 OR v.type LIKE :q2)';
            $params['q1'] = '%' . $filters['search'] . '%';
            $params['q2'] = '%' . $filters['search'] . '%';
        }

        $sql = 'SELECT v.*, t.name AS type_name, t.seats AS type_seats, t.slug AS type_slug
                FROM vehicles v
                LEFT JOIN vehicle_types t ON t.id = v.vehicle_type_id'
            . ($where === [] ? '' : ' WHERE ' . implode(' AND ', $where))
            . ' ORDER BY v.status ASC, v.vehicle_id ASC';

        return $this->fetchAll($sql, $params);
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT v.*, t.name AS type_name, t.seats AS type_seats, t.slug AS type_slug
             FROM vehicles v
             LEFT JOIN vehicle_types t ON t.id = v.vehicle_type_id
             WHERE v.vehicle_id = :id',
            ['id' => $id],
        );
    }

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO vehicles
                (vehicle_type_id, name, type, image, description, price_per_km, price_per_day,
                 status, driver_info, rating, insurance_info)
             VALUES
                (:vehicle_type_id, :name, :type, :image, :description, :price_per_km, :price_per_day,
                 :status, :driver_info, :rating, :insurance_info)',
            $data,
        );
    }

    public function update(int $id, array $data): void
    {
        $allowed = ['vehicle_type_id', 'name', 'type', 'image', 'description', 'price_per_km',
            'price_per_day', 'status', 'driver_info', 'rating', 'insurance_info'];
        $sets = [];
        $params = ['id' => $id];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }
        if ($sets === []) {
            return;
        }
        $this->write('UPDATE vehicles SET ' . implode(', ', $sets) . ' WHERE vehicle_id = :id', $params);
    }

    public function delete(int $id): void
    {
        $this->write('DELETE FROM vehicles WHERE vehicle_id = :id', ['id' => $id]);
    }

    public function countAll(): int
    {
        return (int) $this->scalar('SELECT COUNT(*) FROM vehicles');
    }

    public function countAvailable(): int
    {
        return (int) $this->scalar("SELECT COUNT(*) FROM vehicles WHERE status = 'available'");
    }

    public function countBookings(int $vehicleId): int
    {
        return (int) $this->scalar('SELECT COUNT(*) FROM bookings WHERE vehicle_id = :id', ['id' => $vehicleId]);
    }

    public function updateRating(int $vehicleId, float $rating): void
    {
        $this->write('UPDATE vehicles SET rating = :rating WHERE vehicle_id = :id', [
            'rating' => round($rating, 1),
            'id' => $vehicleId,
        ]);
    }
}
