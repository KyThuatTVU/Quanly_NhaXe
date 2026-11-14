<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoStatisticRepository extends Repository
{
    private const FORMATS = [
        'day' => '%Y-%m-%d',
        'month' => '%Y-%m',
        'year' => '%Y',
    ];

    /**
     * @return array<int, array<string, mixed>>
     */
    public function grouped(string $from, string $to, string $group = 'day'): array
    {
        $format = self::FORMATS[$group] ?? self::FORMATS['day'];

        return $this->fetchAll(
            "SELECT DATE_FORMAT(pickup_date, '{$format}') AS label,
                    COUNT(*) AS total,
                    SUM(status = 'pending') AS pending,
                    SUM(status = 'confirmed') AS confirmed,
                    SUM(status = 'completed') AS completed,
                    SUM(status = 'cancelled') AS cancelled,
                    COALESCE(SUM(total_amount), 0) AS amount
             FROM bookings
             WHERE pickup_date BETWEEN :from AND :to
             GROUP BY label
             ORDER BY label ASC",
            ['from' => $from, 'to' => $to],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(string $from, string $to): array
    {
        $row = $this->fetchOne(
            "SELECT COUNT(*) AS total,
                    SUM(status = 'pending') AS pending,
                    SUM(status = 'confirmed') AS confirmed,
                    SUM(status = 'completed') AS completed,
                    SUM(status = 'cancelled') AS cancelled,
                    COALESCE(SUM(total_amount), 0) AS amount
             FROM bookings
             WHERE pickup_date BETWEEN :from AND :to",
            ['from' => $from, 'to' => $to],
        ) ?? [];

        return [
            'total' => (int) ($row['total'] ?? 0),
            'pending' => (int) ($row['pending'] ?? 0),
            'confirmed' => (int) ($row['confirmed'] ?? 0),
            'completed' => (int) ($row['completed'] ?? 0),
            'cancelled' => (int) ($row['cancelled'] ?? 0),
            'amount' => (float) ($row['amount'] ?? 0),
        ];
    }
}
