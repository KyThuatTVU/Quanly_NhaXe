<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoServiceRepository extends Repository
{
    public function all(bool $activeOnly = false): array
    {
        $where = $activeOnly ? 'WHERE is_active = 1' : '';
        return $this->fetchAll(
            'SELECT * FROM services ' . $where . ' ORDER BY display_order ASC, service_id ASC',
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM services WHERE service_id = :id', ['id' => $id]);
    }

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO services (service_name, slug, description, price, image, is_active, display_order)
             VALUES (:service_name, :slug, :description, :price, :image, :is_active, :display_order)',
            $data,
        );
    }

    public function update(int $id, array $data): void
    {
        $allowed = ['service_name', 'slug', 'description', 'price', 'image', 'is_active', 'display_order'];
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
        $this->write('UPDATE services SET ' . implode(', ', $sets) . ' WHERE service_id = :id', $params);
    }

    public function delete(int $id): void
    {
        $this->write('DELETE FROM services WHERE service_id = :id', ['id' => $id]);
    }

    public function existsSlug(string $slug, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM services WHERE slug = :slug';
        $params = ['slug' => $slug];
        if ($exceptId !== null) {
            $sql .= ' AND service_id <> :id';
            $params['id'] = $exceptId;
        }
        return (int) $this->scalar($sql, $params) > 0;
    }

    public function countBookings(int $id): int
    {
        return (int) $this->scalar('SELECT COUNT(*) FROM bookings WHERE service_id = :id', ['id' => $id]);
    }
}
