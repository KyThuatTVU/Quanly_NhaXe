<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Exceptions\NotFoundException;
use App\Core\Request;
use App\Domain\Enums\BookingStatus;
use App\Domain\Exceptions\BusinessRuleException;
use App\Domain\Exceptions\CarScheduleConflictException;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Repositories\PdoBookingRepository;
use App\Infrastructure\Persistence\Repositories\PdoCarRepository;
use App\Infrastructure\Persistence\Repositories\PdoServiceRepository;
use App\Support\Str;
use App\Support\Validator;

final class BookingService
{
    private const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function __construct(
        private readonly PdoBookingRepository $bookings,
        private readonly PdoCarRepository $cars,
        private readonly PdoServiceRepository $services,
        private readonly AuditLogger $audit,
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters, int $page, int $perPage): array
    {
        return $this->bookings->paginate($filters, $page, $perPage);
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $id): array
    {
        $booking = $this->bookings->find($id);
        if ($booking === null) {
            throw new NotFoundException('Không tìm thấy đơn đặt xe');
        }
        return $booking;
    }

    /**
     * @return array{available: bool, message: string}
     */
    public function checkAvailability(int $vehicleId, string $pickupDate, ?string $returnDate): array
    {
        if (!Validator::isDate($pickupDate)) {
            throw new BusinessRuleException('Ngày đón không đúng định dạng YYYY-MM-DD');
        }
        $returnDate = $returnDate !== null && $returnDate !== '' ? $returnDate : $pickupDate;
        if (!Validator::isDate($returnDate)) {
            throw new BusinessRuleException('Ngày trả không đúng định dạng YYYY-MM-DD');
        }
        if ($returnDate < $pickupDate) {
            throw new BusinessRuleException('Ngày trả phải sau hoặc bằng ngày đón');
        }

        $vehicle = $this->cars->find($vehicleId);
        if ($vehicle === null) {
            throw new NotFoundException('Không tìm thấy xe');
        }
        if ($vehicle['status'] !== 'available') {
            return ['available' => false, 'message' => 'Xe đang tạm ngưng phục vụ'];
        }

        $conflict = $this->bookings->hasConflict($vehicleId, $pickupDate, $returnDate);
        return [
            'available' => !$conflict,
            'message' => $conflict ? 'Xe đã có đơn trong khoảng thời gian này' : 'Xe còn trống trong khoảng thời gian này',
        ];
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed>|null $customer
     * @return array<string, mixed>
     */
    public function create(array $input, ?array $customer, Request $request): array
    {
        $data = Validator::validate($input, [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|phone',
            'customer_email' => 'nullable|email|max:255',
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'nullable|string|max:255',
            'pickup_date' => 'required|date|today_or_future',
            'pickup_time' => 'required|time',
            'return_date' => 'nullable|date',
            'return_time' => 'nullable|time',
            'number_of_passengers' => 'nullable|integer|min:1',
            'vehicle_id' => 'required|integer',
            'service_id' => 'nullable|integer',
            'notes' => 'nullable|string|max:2000',
        ]);

        $pickupDate = (string) $data['pickup_date'];
        $returnDate = $data['return_date'] !== null ? (string) $data['return_date'] : null;

        if ($returnDate !== null && $returnDate < $pickupDate) {
            throw new BusinessRuleException('Ngày trả phải sau hoặc bằng ngày đón');
        }
        if ($returnDate === null && $data['return_time'] !== null) {
            throw new BusinessRuleException('Vui lòng nhập ngày trả khi đã chọn giờ trả');
        }

        $vehicleId = (int) $data['vehicle_id'];
        $vehicle = $this->cars->find($vehicleId);
        if ($vehicle === null) {
            throw new NotFoundException('Không tìm thấy xe');
        }
        if ($vehicle['status'] !== 'available') {
            throw new BusinessRuleException('Xe đang tạm ngưng phục vụ, vui lòng chọn xe khác');
        }

        $passengers = $data['number_of_passengers'] !== null ? (int) $data['number_of_passengers'] : null;
        $seats = isset($vehicle['type_seats']) ? (int) $vehicle['type_seats'] : 0;
        if ($passengers !== null && $seats > 0 && $passengers > $seats) {
            throw new BusinessRuleException("Xe chỉ chở tối đa {$seats} hành khách");
        }

        $service = null;
        if ($data['service_id'] !== null) {
            $service = $this->services->find((int) $data['service_id']);
            if ($service === null) {
                throw new NotFoundException('Không tìm thấy dịch vụ');
            }
        }

        $effectiveReturn = $returnDate ?? $pickupDate;

        $days = max(1, (int) ((strtotime($effectiveReturn) - strtotime($pickupDate)) / 86400) + 1);
        $pricePerDay = $vehicle['price_per_day'] !== null ? (float) $vehicle['price_per_day'] : null;
        $pricePerKm = $vehicle['price_per_km'] !== null ? (float) $vehicle['price_per_km'] : null;
        $totalAmount = $pricePerDay !== null ? round($pricePerDay * $days, 2) : null;

        $customerId = $customer !== null && ($customer['role'] ?? '') === 'customer'
            ? (int) $customer['user_id']
            : null;

        $payload = [
            'booking_code' => $this->generateCode(),
            'customer_id' => $customerId,
            'vehicle_id' => $vehicleId,
            'service_id' => $service['service_id'] ?? null,
            'customer_name' => $data['customer_name'],
            'customer_phone' => Validator::digits((string) $data['customer_phone']),
            'customer_email' => $data['customer_email'],
            'pickup_location' => $data['pickup_location'],
            'dropoff_location' => $data['dropoff_location'],
            'pickup_date' => $pickupDate,
            'pickup_time' => $data['pickup_time'],
            'return_date' => $returnDate,
            'return_time' => $data['return_time'],
            'number_of_passengers' => $passengers,
            'service_type' => $service['service_name'] ?? null,
            'price_per_day' => $pricePerDay,
            'price_per_km' => $pricePerKm,
            'total_amount' => $totalAmount,
            'status' => BookingStatus::Pending->value,
            'notes' => $data['notes'],
        ];

        $id = $this->bookings->createLocked($payload, function () use ($vehicleId, $pickupDate, $effectiveReturn): void {
            if ($this->bookings->hasConflict($vehicleId, $pickupDate, $effectiveReturn)) {
                throw new CarScheduleConflictException();
            }
        });
        if ($id === null) {
            throw new NotFoundException('Không tìm thấy xe');
        }

        $request->setAttribute('booking_code', $payload['booking_code']);
        $this->audit->log(
            $request,
            'booking_create',
            'bookings',
            $id,
            'Tạo đơn ' . $payload['booking_code'] . ' cho ' . $payload['customer_name'],
        );

        return $this->find($id);
    }

    /**
     * @return array<string, mixed>
     */
    public function lookup(string $code, string $phone): array
    {
        $booking = $this->bookings->findByCode(strtoupper(trim($code)));
        if ($booking === null) {
            throw new NotFoundException('Không tìm thấy đơn với mã này');
        }
        if (Validator::digits((string) $booking['customer_phone']) !== Validator::digits($phone)) {
            throw new BusinessRuleException('Số điện thoại không khớp với mã đơn');
        }
        return $booking;
    }

    /**
     * @return array<string, mixed>
     */
    public function updateStatus(int $id, string $status, Request $request): array
    {
        if (!in_array($status, BookingStatus::values(), true)) {
            throw new BusinessRuleException('Trạng thái đơn không hợp lệ');
        }

        $booking = $this->find($id);
        $current = (string) $booking['status'];
        if ($current === $status) {
            return $booking;
        }
        if (!in_array($status, self::TRANSITIONS[$current] ?? [], true)) {
            throw new BusinessRuleException("Không thể chuyển đơn từ '{$current}' sang '{$status}'");
        }

        $this->bookings->updateStatus($id, $status);
        $this->audit->log(
            $request,
            'booking_status',
            'bookings',
            $id,
            "Đổi trạng thái đơn {$booking['booking_code']} từ {$current} sang {$status}",
        );

        return $this->find($id);
    }

    public function delete(int $id, Request $request): void
    {
        $booking = $this->find($id);
        if ($booking['status'] !== BookingStatus::Cancelled->value) {
            throw new BusinessRuleException('Chỉ có thể xóa đơn đã hủy');
        }
        $this->bookings->delete($id);
        $this->audit->log(
            $request,
            'booking_delete',
            'bookings',
            $id,
            'Xóa đơn ' . $booking['booking_code'],
        );
    }

    private function generateCode(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $code = Str::bookingCode();
            if ($this->bookings->findByCode($code) === null) {
                return $code;
            }
        }
        throw new BusinessRuleException('Không sinh được mã đơn, vui lòng thử lại');
    }
}
