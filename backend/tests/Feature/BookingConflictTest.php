<?php

declare(strict_types=1);

use App\Core\Request;
use App\Domain\Exceptions\CarScheduleConflictException;
use App\Domain\Services\BookingService;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Connection;
use App\Infrastructure\Persistence\Repositories\PdoAuditLogRepository;
use App\Infrastructure\Persistence\Repositories\PdoBookingRepository;
use App\Infrastructure\Persistence\Repositories\PdoCarRepository;
use App\Infrastructure\Persistence\Repositories\PdoServiceRepository;

function booking_feature_reset(): void
{
    $pdo = Connection::pdo();
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach (['ratings', 'bookings', 'vehicles', 'vehicle_types', 'services', 'audit_logs', 'rate_limits'] as $table) {
        $pdo->exec('TRUNCATE TABLE ' . $table);
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
}

function booking_feature_vehicle(): int
{
    $pdo = Connection::pdo();
    $slug = 'xe-test-' . bin2hex(random_bytes(3));
    $statement = $pdo->prepare(
        "INSERT INTO vehicle_types (name, slug, seats, price_per_day) VALUES ('Xe test', :slug, 4, 1000000)",
    );
    $statement->execute(['slug' => $slug]);
    $typeId = (int) $pdo->lastInsertId();

    $statement = $pdo->prepare(
        "INSERT INTO vehicles (vehicle_type_id, name, status, price_per_day) VALUES (:type, 'Xe test 4 chỗ', 'available', 1000000)",
    );
    $statement->execute(['type' => $typeId]);

    return (int) $pdo->lastInsertId();
}

/**
 * @return array<string, mixed>
 */
function booking_feature_input(int $vehicleId, string $date): array
{
    return [
        'customer_name' => 'Khách Test',
        'customer_phone' => '0912345678',
        'pickup_location' => 'Hà Nội',
        'pickup_date' => $date,
        'pickup_time' => '08:00',
        'vehicle_id' => $vehicleId,
        'number_of_passengers' => 4,
    ];
}

/**
 * @return array<string, mixed>
 */
function booking_feature_payload(int $vehicleId, string $date): array
{
    return [
        'booking_code' => 'TEST' . strtoupper(bin2hex(random_bytes(4))),
        'customer_id' => null,
        'vehicle_id' => $vehicleId,
        'service_id' => null,
        'customer_name' => 'Khách Test',
        'customer_phone' => '0912345678',
        'customer_email' => null,
        'pickup_location' => 'Hà Nội',
        'dropoff_location' => null,
        'pickup_date' => $date,
        'pickup_time' => '08:00:00',
        'return_date' => null,
        'return_time' => null,
        'number_of_passengers' => 4,
        'service_type' => null,
        'price_per_day' => 1000000,
        'price_per_km' => null,
        'total_amount' => 1000000,
        'status' => 'pending',
        'notes' => null,
    ];
}

function booking_feature_service(): BookingService
{
    return new BookingService(
        new PdoBookingRepository(),
        new PdoCarRepository(),
        new PdoServiceRepository(),
        new AuditLogger(new PdoAuditLogRepository()),
    );
}

function booking_feature_request(): Request
{
    return new Request('POST', '/api/bookings', [], [], [], ['content-type' => 'application/json'], '', '198.51.100.20');
}

return [
    'Đơn thứ hai trùng lịch bị chặn và không ghi vào CSDL' => static function (): void {
        booking_feature_reset();
        $vehicleId = booking_feature_vehicle();
        $date = date('Y-m-d', strtotime('+7 days'));
        $service = booking_feature_service();
        $request = booking_feature_request();

        $first = $service->create(booking_feature_input($vehicleId, $date), null, $request);
        assert_same('pending', $first['status']);

        assert_throws(
            CarScheduleConflictException::class,
            static fn (): array => $service->create(booking_feature_input($vehicleId, $date), null, $request),
        );

        assert_same(1, (int) Connection::pdo()->query('SELECT COUNT(*) FROM bookings')->fetchColumn());
    },

    'Hai xe khác nhau được đặt cùng ngày' => static function (): void {
        booking_feature_reset();
        $firstVehicle = booking_feature_vehicle();
        $secondVehicle = booking_feature_vehicle();
        $date = date('Y-m-d', strtotime('+7 days'));
        $service = booking_feature_service();
        $request = booking_feature_request();

        $service->create(booking_feature_input($firstVehicle, $date), null, $request);
        $service->create(booking_feature_input($secondVehicle, $date), null, $request);

        assert_same(2, (int) Connection::pdo()->query('SELECT COUNT(*) FROM bookings')->fetchColumn());
    },

    'Guard lỗi thì giao dịch rollback, không để lại đơn' => static function (): void {
        booking_feature_reset();
        $vehicleId = booking_feature_vehicle();
        $repository = new PdoBookingRepository();

        assert_throws(RuntimeException::class, static function () use ($repository, $vehicleId): void {
            $repository->createLocked(
                booking_feature_payload($vehicleId, date('Y-m-d', strtotime('+8 days'))),
                static function (): void {
                    throw new RuntimeException('guard dừng giao dịch');
                },
            );
        });

        assert_same(0, (int) Connection::pdo()->query('SELECT COUNT(*) FROM bookings')->fetchColumn());
    },

    'createLocked trả null khi xe không tồn tại' => static function (): void {
        booking_feature_reset();
        $repository = new PdoBookingRepository();

        $result = $repository->createLocked(
            booking_feature_payload(999999, date('Y-m-d', strtotime('+9 days'))),
            static function (): void {
            },
        );

        assert_same(null, $result);
        assert_same(0, (int) Connection::pdo()->query('SELECT COUNT(*) FROM bookings')->fetchColumn());
    },
];
