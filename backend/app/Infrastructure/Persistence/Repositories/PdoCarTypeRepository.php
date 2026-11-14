<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoCarTypeRepository extends Repository
{
    public function all(bool $activeOnly = false): array
    {
        $where = $activeOnly ? 'WHERE is_active = 1' : '';
        return $this->fetchAll(
            'SELECT * FROM vehicle_types ' . $where . ' ORDER BY display_order ASC, id ASC',
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM vehicle_types WHERE id = :id', ['id' => $id]);
    }

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO vehicle_types
                (name, slug, seats, seats_min, seats_max, description, icon, image_url,
                 price_per_day, price_per_km, features, is_active, display_order)
             VALUES
                (:name, :slug, :seats, :seats_min, :seats_max, :description, :icon, :image_url,
                 :price_per_day, :price_per_km, :features, :is_active, :display_order)',
            $data,
        );
    }

    public function update(int $id, array $data): void
    {
        $allowed = ['name', 'slug', 'seats', 'seats_min', 'seats_max', 'description', 'icon', 'image_url',
            'price_per_day', 'price_per_km', 'features', 'is_active', 'display_order'];
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
        $this->write('UPDATE vehicle_types SET ' . implode(', ', $sets) . ' WHERE id = :id', $params);
    }

    public function delete(int $id): void
    {
        $this->write('DELETE FROM vehicle_types WHERE id = :id', ['id' => $id]);
    }

    public function countVehicles(int $id): int
    {
        return (int) $this->scalar('SELECT COUNT(*) FROM vehicles WHERE vehicle_type_id = :id', ['id' => $id]);
    }

    public function existsSlug(string $slug, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM vehicle_types WHERE slug = :slug';
        $params = ['slug' => $slug];
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $exceptId;
        }
        return (int) $this->scalar($sql, $params) > 0;
    }
}
