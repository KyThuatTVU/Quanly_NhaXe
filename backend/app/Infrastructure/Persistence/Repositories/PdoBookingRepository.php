<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Persistence\Repository;

final class PdoBookingRepository extends Repository
{
    private const SELECT = 'SELECT b.*, v.name AS vehicle_name, v.type AS vehicle_type, v.image AS vehicle_image,
                s.service_name
         FROM bookings b
         LEFT JOIN vehicles v ON v.vehicle_id = b.vehicle_id
         LEFT JOIN services s ON s.service_id = b.service_id';

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO bookings
                (booking_code, customer_id, vehicle_id, service_id, customer_name, customer_phone,
                 customer_email, pickup_location, dropoff_location, pickup_date, pickup_time,
                 return_date, return_time, number_of_passengers, service_type,
                 price_per_day, price_per_km, total_amount, status, notes)
             VALUES
                (:booking_code, :customer_id, :vehicle_id, :service_id, :customer_name, :customer_phone,
                 :customer_email, :pickup_location, :dropoff_location, :pickup_date, :pickup_time,
                 :return_date, :return_time, :number_of_passengers, :service_type,
                 :price_per_day, :price_per_km, :total_amount, :status, :notes)',
            $data,
        );
    }

    /**
     * Khóa dòng xe rồi mới kiểm tra trùng lịch + ghi đơn, tránh 2 khách đặt cùng lúc.
     * $guard ném ngoại lệ nếu khoảng ngày bị trùng; trả null khi xe không tồn tại.
     */
    public function createLocked(array $data, callable $guard): ?int
    {
        $this->pdo->beginTransaction();
        try {
            $lock = $this->pdo->prepare('SELECT vehicle_id FROM vehicles WHERE vehicle_id = :id FOR UPDATE');
            $lock->execute(['id' => (int) ($data['vehicle_id'] ?? 0)]);
            if ($lock->fetchColumn() === false) {
                $this->pdo->rollBack();
                return null;
            }

            $guard();

            $id = $this->create($data);
            $this->pdo->commit();
            return $id;
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne(self::SELECT . ' WHERE b.booking_id = :id', ['id' => $id]);
    }

    public function findByCode(string $code): ?array
    {
        return $this->fetchOne(self::SELECT . ' WHERE b.booking_code = :code', ['code' => $code]);
    }

    public function paginate(array $filters, int $page, int $perPage): array
    {
        [$page, $offset] = $this->paging($page, $perPage);
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'b.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['from'])) {
            $where[] = 'b.pickup_date >= :from';
            $params['from'] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $where[] = 'b.pickup_date <= :to';
            $params['to'] = $filters['to'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(b.booking_code LIKE :q1 OR b.customer_name LIKE :q2 OR b.customer_phone LIKE :q3)';
            $like = '%' . $filters['search'] . '%';
            $params['q1'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);
        $total = (int) $this->scalar('SELECT COUNT(*) FROM bookings b' . $whereSql, $params);

        $items = $this->fetchAll(
            self::SELECT . $whereSql . ' ORDER BY b.booking_id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params,
        );

        return $this->paged($items, $total, $page, $perPage);
    }

    public function updateStatus(int $id, string $status): void
    {
        $this->write('UPDATE bookings SET status = :status WHERE booking_id = :id', [
            'status' => $status,
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->write('DELETE FROM bookings WHERE booking_id = :id', ['id' => $id]);
    }

    /**
     * Kiểm tra trùng lịch: đơn mới chồng khoảng ngày với đơn pending/confirmed của cùng xe.
     */
    public function hasConflict(int $vehicleId, string $pickupDate, string $returnDate, ?int $exceptId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM bookings
                WHERE vehicle_id = :vehicle_id
                  AND status IN ('pending','confirmed')
                  AND COALESCE(return_date, pickup_date) >= :pickup_date
                  AND pickup_date <= :return_date";
        $params = [
            'vehicle_id' => $vehicleId,
            'pickup_date' => $pickupDate,
            'return_date' => $returnDate,
        ];
        if ($exceptId !== null) {
            $sql .= ' AND booking_id <> :except_id';
            $params['except_id'] = $exceptId;
        }
        return (int) $this->scalar($sql, $params) > 0;
    }

    public function statusCounts(?string $from = null, ?string $to = null): array
    {
        $where = [];
        $params = [];
        if ($from !== null) {
            $where[] = 'pickup_date >= :from';
            $params['from'] = $from;
        }
        if ($to !== null) {
            $where[] = 'pickup_date <= :to';
            $params['to'] = $to;
        }
        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

        $result = [
            'pending' => ['count' => 0, 'amount' => 0.0],
            'confirmed' => ['count' => 0, 'amount' => 0.0],
            'completed' => ['count' => 0, 'amount' => 0.0],
            'cancelled' => ['count' => 0, 'amount' => 0.0],
        ];
        $rows = $this->fetchAll(
            'SELECT status, COUNT(*) AS total, COALESCE(SUM(total_amount), 0) AS amount
             FROM bookings' . $whereSql . ' GROUP BY status',
            $params,
        );
        foreach ($rows as $row) {
            $result[$row['status']] = [
                'count' => (int) $row['total'],
                'amount' => (float) $row['amount'],
            ];
        }
        return $result;
    }

    public function countAll(): int
    {
        return (int) $this->scalar('SELECT COUNT(*) FROM bookings');
    }

    public function recent(int $limit = 5): array
    {
        return $this->fetchAll(self::SELECT . ' ORDER BY b.booking_id DESC LIMIT ' . max(1, $limit));
    }

    public function countByCustomer(int $customerId): int
    {
        return (int) $this->scalar('SELECT COUNT(*) FROM bookings WHERE customer_id = :id', ['id' => $customerId]);
    }
}
