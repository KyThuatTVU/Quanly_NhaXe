<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoUserRepository extends Repository
{
    public function find(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT user_id, username, email, phone, role, is_active, last_login_at, created_at, updated_at
             FROM users WHERE user_id = :id',
            ['id' => $id],
        );
    }

    public function findByLogin(string $login): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM users WHERE username = :login OR email = :email LIMIT 1',
            ['login' => $login, 'email' => $login],
        );
    }

    public function all(?string $search, int $page, int $perPage): array
    {
        [$page, $offset] = $this->paging($page, $perPage);
        $where = '';
        $params = [];
        if ($search !== null && $search !== '') {
            $where = 'WHERE (username LIKE :q1 OR email LIKE :q2 OR phone LIKE :q3)';
            $like = '%' . $search . '%';
            $params = ['q1' => $like, 'q2' => $like, 'q3' => $like];
        }

        $total = (int) $this->scalar('SELECT COUNT(*) FROM users ' . $where, $params);
        $items = $this->fetchAll(
            'SELECT user_id, username, email, phone, role, is_active, last_login_at, created_at
             FROM users ' . $where . ' ORDER BY user_id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params,
        );

        return $this->paged($items, $total, $page, $perPage);
    }

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO users (username, password, email, phone, role, is_active)
             VALUES (:username, :password, :email, :phone, :role, :is_active)',
            $data,
        );
    }

    public function update(int $id, array $data): void
    {
        if ($data === []) {
            return;
        }
        $allowed = ['email', 'phone', 'role', 'is_active', 'password'];
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
        $this->write('UPDATE users SET ' . implode(', ', $sets) . ' WHERE user_id = :id', $params);
    }

    public function delete(int $id): void
    {
        $this->write('DELETE FROM users WHERE user_id = :id', ['id' => $id]);
    }

    public function countActiveAdmins(?int $exceptId = null): int
    {
        $sql = "SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1";
        $params = [];
        if ($exceptId !== null) {
            $sql .= ' AND user_id <> :id';
            $params['id'] = $exceptId;
        }
        return (int) $this->scalar($sql, $params);
    }

    public function hasBusinessData(int $id): bool
    {
        $bookings = (int) $this->scalar('SELECT COUNT(*) FROM bookings WHERE customer_id = :id', ['id' => $id]);
        $ratings = (int) $this->scalar('SELECT COUNT(*) FROM ratings WHERE customer_id = :id', ['id' => $id]);
        return $bookings + $ratings > 0;
    }

    public function touchLogin(int $id): void
    {
        $this->write('UPDATE users SET last_login_at = NOW() WHERE user_id = :id', ['id' => $id]);
    }

    public function updatePassword(int $id, string $hash): void
    {
        $this->write('UPDATE users SET password = :password WHERE user_id = :id', ['id' => $id, 'password' => $hash]);
    }

    public function existsUsername(string $username, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE username = :username';
        $params = ['username' => $username];
        if ($exceptId !== null) {
            $sql .= ' AND user_id <> :id';
            $params['id'] = $exceptId;
        }
        return (int) $this->scalar($sql, $params) > 0;
    }

    public function existsEmail(?string $email, ?int $exceptId = null): bool
    {
        if ($email === null || $email === '') {
            return false;
        }
        $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
        $params = ['email' => $email];
        if ($exceptId !== null) {
            $sql .= ' AND user_id <> :id';
            $params['id'] = $exceptId;
        }
        return (int) $this->scalar($sql, $params) > 0;
    }

    public function countCustomersWithOrders(): int
    {
        return (int) $this->scalar(
            "SELECT COUNT(DISTINCT customer_id) FROM bookings WHERE customer_id IS NOT NULL",
        );
    }
}
