<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Enums\BookingStatus;
use App\Domain\Services\BookingService;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Support\Flash;
use App\Support\Paginator;

final class BookingController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly BookingService $bookings,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        $filters = [
            'status' => (string) $request->query('status', ''),
            'from' => (string) $request->query('from', ''),
            'to' => (string) $request->query('to', ''),
            'search' => (string) $request->query('search', ''),
        ];
        $bookings = $this->bookings->paginate($filters, $this->page($request), $this->perPage($request));

        return $this->adminView('admin/bookings/index', $request, [
            'title' => 'Quản lý đơn đặt xe',
            'bookings' => $bookings,
            'filters' => $filters,
            'statuses' => BookingStatus::values(),
            'pagination' => Paginator::window($bookings, url('/admin/bookings'), $filters),
        ]);
    }

    public function show(Request $request, string $id): Response
    {
        return $this->adminView('admin/bookings/show', $request, [
            'title' => 'Chi tiết đơn đặt xe',
            'booking' => $this->bookings->find($this->intParam($id)),
            'statuses' => BookingStatus::values(),
        ]);
    }

    public function updateStatus(Request $request, string $id): Response
    {
        $booking = $this->bookings->updateStatus(
            $this->intParam($id),
            (string) $request->input('status', ''),
            $request,
        );
        Flash::put('success', 'Đã cập nhật trạng thái đơn ' . $booking['booking_code']);
        return $this->back($request, '/admin/bookings');
    }

    public function destroy(Request $request, string $id): Response
    {
        $booking = $this->bookings->find($this->intParam($id));
        $this->bookings->delete((int) $booking['booking_id'], $request);
        Flash::put('success', 'Đã xóa đơn ' . $booking['booking_code']);
        return $this->redirect('/admin/bookings');
    }
}
