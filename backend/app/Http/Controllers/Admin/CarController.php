<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Enums\CarStatus;
use App\Domain\Services\CarService;
use App\Domain\Services\CarTypeService;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Support\Flash;

final class CarController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly CarService $cars,
        private readonly CarTypeService $types,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        $filters = [
            'status' => (string) $request->query('status', ''),
            'vehicle_type_id' => (string) $request->query('vehicle_type_id', ''),
            'search' => (string) $request->query('search', ''),
        ];

        return $this->adminView('admin/cars/index', $request, [
            'title' => 'Quản lý xe',
            'cars' => $this->cars->all(array_filter($filters, static fn ($v) => $v !== '')),
            'types' => $this->types->all(),
            'filters' => $filters,
            'statuses' => CarStatus::values(),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->adminView('admin/cars/form', $request, [
            'title' => 'Thêm xe',
            'car' => null,
            'types' => $this->types->all(),
            'statuses' => CarStatus::values(),
        ]);
    }

    public function store(Request $request): Response
    {
        $car = $this->cars->create($this->withUpload($request, $request->all(), 'cars'), $request);
        Flash::put('success', 'Đã thêm xe ' . $car['name']);
        return $this->redirect('/admin/cars');
    }

    public function edit(Request $request, string $id): Response
    {
        return $this->adminView('admin/cars/form', $request, [
            'title' => 'Sửa xe',
            'car' => $this->cars->find($this->intParam($id)),
            'types' => $this->types->all(),
            'statuses' => CarStatus::values(),
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $id = $this->intParam($id);
        $data = $this->withUpload($request, $request->all(), 'cars');
        if (array_key_exists('vehicle_type_id', $data) && ($data['vehicle_type_id'] === '' || $data['vehicle_type_id'] === null)) {
            unset($data['vehicle_type_id']);
        }
        $car = $this->cars->update($id, $data, $request);
        Flash::put('success', 'Đã cập nhật xe ' . $car['name']);
        return $this->redirect('/admin/cars');
    }

    public function destroy(Request $request, string $id): Response
    {
        $id = $this->intParam($id);
        $this->cars->delete($id, $request);
        Flash::put('success', 'Đã xóa xe #' . $id);
        return $this->redirect('/admin/cars');
    }
}
