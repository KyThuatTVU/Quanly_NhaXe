<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Exceptions\BusinessRuleException;
use App\Infrastructure\Persistence\Repositories\PdoBookingRepository;
use App\Infrastructure\Persistence\Repositories\PdoCarRepository;
use App\Infrastructure\Persistence\Repositories\PdoContactRepository;
use App\Infrastructure\Persistence\Repositories\PdoStatisticRepository;
use App\Infrastructure\Persistence\Repositories\PdoUserRepository;
use App\Support\Validator;

final class StatisticService
{
    private const GROUPS = ['day', 'month', 'year'];

    public function __construct(
        private readonly PdoStatisticRepository $statistics,
        private readonly PdoBookingRepository $bookings,
        private readonly PdoCarRepository $cars,
        private readonly PdoContactRepository $contacts,
        private readonly PdoUserRepository $users,
    ) {
    }

    /**
     * Số liệu tổng hợp cho trang quản trị.
     *
     * @return array<string, mixed>
     */
    public function dashboard(): array
    {
        $statusCounts = $this->bookings->statusCounts();

        return [
            'cars' => [
                'total' => $this->cars->countAll(),
                'available' => $this->cars->countAvailable(),
            ],
            'bookings' => [
                'total' => $this->bookings->countAll(),
                'by_status' => $statusCounts,
            ],
            'customers' => [
                'with_orders' => $this->users->countCustomersWithOrders(),
            ],
            'contacts' => [
                'new' => $this->contacts->countNew(),
            ],
            'recent_bookings' => $this->bookings->recent(5),
        ];
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, from: string, to: string, group: string}
     */
    public function grouped(?string $from, ?string $to, ?string $group): array
    {
        [$from, $to] = $this->range($from, $to);
        $group = $group !== null && $group !== '' ? $group : 'day';
        if (!in_array($group, self::GROUPS, true)) {
            throw new BusinessRuleException('Kiểu gom nhóm phải là day, month hoặc year');
        }

        $items = array_map(static fn (array $row): array => [
            'label' => $row['label'],
            'total' => (int) $row['total'],
            'pending' => (int) $row['pending'],
            'confirmed' => (int) $row['confirmed'],
            'completed' => (int) $row['completed'],
            'cancelled' => (int) $row['cancelled'],
            'amount' => (float) $row['amount'],
        ], $this->statistics->grouped($from, $to, $group));

        return [
            'items' => $items,
            'from' => $from,
            'to' => $to,
            'group' => $group,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(?string $from, ?string $to): array
    {
        [$from, $to] = $this->range($from, $to);
        return ['summary' => $this->statistics->summary($from, $to), 'from' => $from, 'to' => $to];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function range(?string $from, ?string $to): array
    {
        $from = $from !== null && $from !== '' ? $from : date('Y-m-01');
        $to = $to !== null && $to !== '' ? $to : date('Y-m-t');

        if (!Validator::isDate($from) || !Validator::isDate($to)) {
            throw new BusinessRuleException('Khoảng thời gian phải có định dạng YYYY-MM-DD');
        }
        if ($from > $to) {
            throw new BusinessRuleException('Ngày bắt đầu phải trước hoặc bằng ngày kết thúc');
        }
        return [$from, $to];
    }
}
