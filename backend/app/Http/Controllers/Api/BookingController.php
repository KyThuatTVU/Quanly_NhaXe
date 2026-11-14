<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\BookingService;

final class BookingController extends ApiController
{
    public function __construct(private readonly BookingService $bookings)
    {
    }

    public function checkAvailability(Request $request): Response
    {
        $vehicleId = (int) $request->input('vehicle_id', 0);
        $pickupDate = (string) $request->input('pickup_date', '');
        $returnDate = $request->input('return_date');
        if ($vehicleId <= 0) {
            throw new \App\Core\Exceptions\ValidationException(['vehicle_id' => 'Xe không được để trống.']);
        }

        $result = $this->bookings->checkAvailability(
            $vehicleId,
            $pickupDate,
            $returnDate !== null ? (string) $returnDate : null,
        );
        return Response::ok($result, $result['message']);
    }

    public function store(Request $request): Response
    {
        $booking = $this->bookings->create($request->all(), $request->user(), $request);
        return Response::created($booking, 'Đặt xe thành công, mã đơn của bạn là ' . $booking['booking_code']);
    }

    public function lookup(Request $request): Response
    {
        $code = (string) $request->input('code', '');
        $phone = (string) $request->input('phone', '');
        if ($code === '' || $phone === '') {
            throw new \App\Core\Exceptions\ValidationException([
                'code' => 'Vui lòng nhập mã đơn.',
                'phone' => 'Vui lòng nhập số điện thoại.',
            ]);
        }
        return Response::ok($this->bookings->lookup($code, $phone));
    }

    public function adminIndex(Request $request): Response
    {
        $filters = [
            'status' => $request->query('status'),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
            'search' => $request->query('search'),
        ];
        return Response::ok($this->bookings->paginate($filters, $this->page($request), $this->perPage($request)));
    }

    public function adminShow(string $id): Response
    {
        return Response::ok($this->bookings->find($this->id($id)));
    }

    public function updateStatus(Request $request, string $id): Response
    {
        $status = (string) $request->input('status', '');
        if ($status === '') {
            throw new \App\Core\Exceptions\ValidationException(['status' => 'Trạng thái không được để trống.']);
        }
        return Response::ok(
            $this->bookings->updateStatus($this->id($id), $status, $request),
            'Cập nhật trạng thái đơn thành công',
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        $this->bookings->delete($this->id($id), $request);
        return Response::ok(null, 'Xóa đơn đặt xe thành công');
    }
}
