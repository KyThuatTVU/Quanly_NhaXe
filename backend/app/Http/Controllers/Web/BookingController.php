<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Exceptions\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Domain\Exceptions\BusinessRuleException;
use App\Domain\Services\BookingService;
use App\Domain\Services\CarService;
use App\Domain\Services\ServiceCatalogService;
use App\Domain\Services\SettingService;
use App\Support\Flash;

final class BookingController extends WebController
{
    public function __construct(
        SettingService $settings,
        private readonly BookingService $bookings,
        private readonly CarService $cars,
        private readonly ServiceCatalogService $services,
    ) {
        parent::__construct($settings);
    }

    public function index(Request $request): Response
    {
        return $this->view('web/booking/index', [
            'title' => 'Đặt xe',
            'vehicles' => $this->cars->all(['status' => 'available']),
            'services' => $this->services->all(true),
            'selectedVehicle' => (int) $request->query('vehicle_id', 0),
        ]);
    }

    public function store(Request $request): Response
    {
        $booking = $this->bookings->create($request->all(), $request->user(), $request);
        Flash::put('booking', $booking);
        return $this->redirect('/dat-xe/thanh-cong');
    }

    public function success(Request $request): Response
    {
        $booking = Flash::pull('booking');
        if (!is_array($booking)) {
            Flash::put('error', 'Không tìm thấy thông tin đơn vừa đặt');
            return $this->redirect('/dat-xe');
        }
        return $this->view('web/booking/success', [
            'title' => 'Đặt xe thành công',
            'booking' => $booking,
        ]);
    }

    public function lookupForm(Request $request): Response
    {
        return $this->view('web/booking/lookup', [
            'title' => 'Tra cứu đơn',
            'booking' => null,
            'error' => null,
            'code' => '',
            'phone' => '',
        ]);
    }

    public function lookup(Request $request): Response
    {
        $code = trim((string) $request->input('code', ''));
        $phone = trim((string) $request->input('phone', ''));
        $booking = null;
        $error = null;

        if ($code === '' || $phone === '') {
            $error = 'Vui lòng nhập mã đơn và số điện thoại';
        } else {
            try {
                $booking = $this->bookings->lookup($code, $phone);
            } catch (HttpException | BusinessRuleException $exception) {
                $error = $exception->getMessage();
            }
        }

        return $this->view('web/booking/lookup', [
            'title' => 'Tra cứu đơn',
            'booking' => $booking,
            'error' => $error,
            'code' => $code,
            'phone' => $phone,
        ]);
    }
}
