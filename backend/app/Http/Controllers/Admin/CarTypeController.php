<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\CarTypeService;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Support\Flash;

final class CarTypeController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly CarTypeService $types,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        return $this->adminView('admin/car-types/index', $request, [
            'title' => 'Loại xe',
            'types' => $this->types->all(),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->adminView('admin/car-types/form', $request, [
            'title' => 'Thêm loại xe',
            'type' => null,
        ]);
    }

    public function store(Request $request): Response
    {
        $type = $this->types->create($request->all(), $request);
        Flash::put('success', 'Đã thêm loại xe ' . $type['name']);
        return $this->redirect('/admin/car-types');
    }

    public function edit(Request $request, string $id): Response
    {
        return $this->adminView('admin/car-types/form', $request, [
            'title' => 'Sửa loại xe',
            'type' => $this->types->find($this->intParam($id)),
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $type = $this->types->update($this->intParam($id), $request->all(), $request);
        Flash::put('success', 'Đã cập nhật loại xe ' . $type['name']);
        return $this->redirect('/admin/car-types');
    }

    public function destroy(Request $request, string $id): Response
    {
        $id = $this->intParam($id);
        $this->types->delete($id, $request);
        Flash::put('success', 'Đã xóa loại xe #' . $id);
        return $this->redirect('/admin/car-types');
    }
}
