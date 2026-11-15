<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\CarService;
use App\Domain\Services\CarTypeService;
use App\Domain\Services\ReviewService;
use App\Domain\Services\SettingService;

final class CarController extends WebController
{
    public function __construct(
        SettingService $settings,
        private readonly CarService $cars,
        private readonly CarTypeService $types,
        private readonly ReviewService $reviews,
    ) {
        parent::__construct($settings);
    }

    public function index(Request $request): Response
    {
        return $this->view('web/cars/index', [
            'title' => 'Danh sách xe',
            'types' => $this->types->all(true),
            'cars' => $this->cars->all([
                'vehicle_type_id' => $request->query('vehicle_type_id'),
                'search' => $request->query('search'),
                'status' => 'available',
            ]),
            'filters' => [
                'vehicle_type_id' => (string) $request->query('vehicle_type_id', ''),
                'search' => (string) $request->query('search', ''),
            ],
        ]);
    }

    public function show(Request $request, string $id): Response
    {
        $car = $this->cars->find($this->intParam($id));
        return $this->view('web/cars/show', [
            'title' => $car['name'],
            'car' => $car,
            'reviews' => $this->reviews->byVehicle((int) $car['vehicle_id']),
        ]);
    }
}
