<?php

declare(strict_types=1);

namespace App\Support;

final class Paginator
{
    /**
     * @param array<string, mixed> $paginator
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public static function window(array $paginator, string $baseUrl, array $query = []): array
    {
        $page = max(1, (int) ($paginator['page'] ?? 1));
        $last = max(1, (int) ($paginator['last_page'] ?? 1));
        $link = static function (int $target) use ($baseUrl, $query): string {
            $query['page'] = $target;
            return $baseUrl . '?' . http_build_query($query);
        };

        $start = max(1, $page - 2);
        $end = min($last, $page + 2);
        $pages = [];
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = ['page' => $i, 'url' => $link($i), 'current' => $i === $page];
        }

        return [
            'pages' => $pages,
            'prev' => $page > 1 ? $link($page - 1) : null,
            'next' => $page < $last ? $link($page + 1) : null,
            'current' => $page,
            'last' => $last,
            'total' => (int) ($paginator['total'] ?? 0),
            'per_page' => (int) ($paginator['per_page'] ?? 15),
        ];
    }
}
