<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

abstract class Repository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::pdo();
    }

    /**
     * @param array<string, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function scalar(string $sql, array $params = []): mixed
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchColumn();
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function write(string $sql, array $params = []): int
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->rowCount();
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function insert(string $sql, array $params = []): int
    {
        $this->write($sql, $params);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array{items: array, total: int, page: int, per_page: int, last_page: int}
     */
    protected function paged(array $items, int $total, int $page, int $perPage): array
    {
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => max(1, (int) ceil($total / max(1, $perPage))),
        ];
    }

    /**
     * @return array{0: int, 1: int} [page, offset]
     */
    protected function paging(int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        return [$page, ($page - 1) * $perPage];
    }
}
