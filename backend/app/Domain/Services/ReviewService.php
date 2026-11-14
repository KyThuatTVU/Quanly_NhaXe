<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Exceptions\NotFoundException;
use App\Core\Request;
use App\Domain\Enums\BookingStatus;
use App\Domain\Exceptions\BusinessRuleException;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Repositories\PdoBookingRepository;
use App\Infrastructure\Persistence\Repositories\PdoCarRepository;
use App\Infrastructure\Persistence\Repositories\PdoReviewRepository;
use App\Support\Validator;

final class ReviewService
{
    public function __construct(
        private readonly PdoReviewRepository $reviews,
        private readonly PdoBookingRepository $bookings,
        private readonly PdoCarRepository $cars,
        private readonly AuditLogger $audit,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function byVehicle(int $vehicleId): array
    {
        $vehicle = $this->cars->find($vehicleId);
        if ($vehicle === null) {
            throw new NotFoundException('Không tìm thấy xe');
        }
        return $this->reviews->byVehicle($vehicleId);
    }

    /**
     * @return array<string, mixed>
     */
    public function paginate(?bool $visible, int $page, int $perPage): array
    {
        return $this->reviews->paginate($visible === null ? null : ($visible ? 1 : 0), $page, $perPage);
    }

    /**
     * Khách gửi đánh giá: chỉ khi đơn đã hoàn thành, đúng số điện thoại, mỗi đơn một lần.
     *
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function create(array $input, Request $request): array
    {
        $data = Validator::validate($input, [
            'booking_code' => 'required|string|max:20',
            'phone' => 'required|phone',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $booking = $this->bookings->findByCode(strtoupper((string) $data['booking_code']));
        if ($booking === null) {
            throw new NotFoundException('Không tìm thấy đơn với mã này');
        }
        if (Validator::digits((string) $booking['customer_phone']) !== Validator::digits((string) $data['phone'])) {
            throw new BusinessRuleException('Số điện thoại không khớp với mã đơn');
        }
        if ($booking['status'] !== BookingStatus::Completed->value) {
            throw new BusinessRuleException('Chỉ có thể đánh giá khi đơn đã hoàn thành');
        }
        if ($booking['vehicle_id'] === null) {
            throw new BusinessRuleException('Đơn này không gắn với xe nào để đánh giá');
        }
        $bookingId = (int) $booking['booking_id'];
        if ($this->reviews->existsForBooking($bookingId)) {
            throw new BusinessRuleException('Đơn này đã được đánh giá');
        }

        $vehicleId = (int) $booking['vehicle_id'];
        $id = $this->reviews->create([
            'booking_id' => $bookingId,
            'vehicle_id' => $vehicleId,
            'customer_id' => $booking['customer_id'] !== null ? (int) $booking['customer_id'] : null,
            'rating' => (int) $data['rating'],
            'comment' => $data['comment'],
            'is_visible' => 1,
        ]);

        $this->refreshVehicleRating($vehicleId);
        $this->audit->log(
            $request,
            'review_create',
            'ratings',
            $id,
            'Đánh giá ' . $data['rating'] . ' sao cho đơn ' . $booking['booking_code'],
        );

        return $this->reviews->find($id) ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function setVisible(int $id, bool $visible, Request $request): array
    {
        $review = $this->reviews->find($id);
        if ($review === null) {
            throw new NotFoundException('Không tìm thấy đánh giá');
        }
        $this->reviews->setVisible($id, $visible);
        $this->refreshVehicleRating((int) $review['vehicle_id']);
        $this->audit->log(
            $request,
            $visible ? 'review_show' : 'review_hide',
            'ratings',
            $id,
            ($visible ? 'Hiện' : 'Ẩn') . ' đánh giá #' . $id,
        );
        return $this->reviews->find($id) ?? [];
    }

    public function delete(int $id, Request $request): void
    {
        $review = $this->reviews->find($id);
        if ($review === null) {
            throw new NotFoundException('Không tìm thấy đánh giá');
        }
        $this->reviews->delete($id);
        $this->refreshVehicleRating((int) $review['vehicle_id']);
        $this->audit->log($request, 'review_delete', 'ratings', $id, 'Xóa đánh giá #' . $id);
    }

    private function refreshVehicleRating(int $vehicleId): void
    {
        $this->cars->updateRating($vehicleId, $this->reviews->average($vehicleId));
    }
}
