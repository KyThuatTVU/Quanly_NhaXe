<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoPostRepository extends Repository
{
    public function published(int $limit = 6, ?string $category = null): array
    {
        $sql = "SELECT post_id, title, slug, image, category, created_at
                FROM posts WHERE status = 'published'";
        $params = [];
        if ($category !== null && $category !== '') {
            $sql .= ' AND category = :category';
            $params['category'] = $category;
        }
        $sql .= ' ORDER BY created_at DESC LIMIT ' . max(1, min(50, $limit));
        return $this->fetchAll($sql, $params);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->fetchOne(
            "SELECT p.*, u.username AS author_name
             FROM posts p
             LEFT JOIN users u ON u.user_id = p.created_by
             WHERE p.slug = :slug",
            ['slug' => $slug],
        );
    }

    public function paginate(array $filters, int $page, int $perPage): array
    {
        [$page, $offset] = $this->paging($page, $perPage);
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'p.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['category'])) {
            $where[] = 'p.category = :category';
            $params['category'] = $filters['category'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(p.title LIKE :q1 OR p.slug LIKE :q2)';
            $params['q1'] = '%' . $filters['search'] . '%';
            $params['q2'] = '%' . $filters['search'] . '%';
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);
        $total = (int) $this->scalar('SELECT COUNT(*) FROM posts p' . $whereSql, $params);
        $items = $this->fetchAll(
            'SELECT p.*, u.username AS author_name FROM posts p
             LEFT JOIN users u ON u.user_id = p.created_by'
            . $whereSql . ' ORDER BY p.post_id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params,
        );

        return $this->paged($items, $total, $page, $perPage);
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM posts WHERE post_id = :id', ['id' => $id]);
    }

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO posts (title, slug, content, image, category, status, created_by)
             VALUES (:title, :slug, :content, :image, :category, :status, :created_by)',
            $data,
        );
    }

    public function update(int $id, array $data): void
    {
        $allowed = ['title', 'slug', 'content', 'image', 'category', 'status'];
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
        $this->write('UPDATE posts SET ' . implode(', ', $sets) . ' WHERE post_id = :id', $params);
    }

    public function delete(int $id): void
    {
        $this->write('DELETE FROM posts WHERE post_id = :id', ['id' => $id]);
    }

    public function categories(): array
    {
        $rows = $this->fetchAll(
            "SELECT category, COUNT(*) AS total FROM posts
             WHERE category IS NOT NULL AND category <> ''
             GROUP BY category ORDER BY category ASC",
        );
        return array_map(static fn (array $row): array => [
            'name' => $row['category'],
            'total' => (int) $row['total'],
        ], $rows);
    }

    public function existsSlug(string $slug, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM posts WHERE slug = :slug';
        $params = ['slug' => $slug];
        if ($exceptId !== null) {
            $sql .= ' AND post_id <> :id';
            $params['id'] = $exceptId;
        }
        return (int) $this->scalar($sql, $params) > 0;
    }
}
