<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoSettingRepository extends Repository
{
    public function get(): ?array
    {
        return $this->fetchOne('SELECT * FROM settings WHERE id = 1');
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(array $data): void
    {
        $allowed = ['company_name', 'address', 'phone', 'hotline', 'zalo', 'facebook',
            'email', 'working_hours', 'map_location', 'description'];
        $sets = [];
        $params = ['id' => 1];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }
        if ($sets === []) {
            return;
        }

        $exists = $this->get() !== null;
        if (!$exists) {
            $columns = array_keys($params);
            $this->write(
                'INSERT INTO settings (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', $columns) . ')',
                $params,
            );
            return;
        }
        $this->write('UPDATE settings SET ' . implode(', ', $sets) . ' WHERE id = :id', $params);
    }
}
