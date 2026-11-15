<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\ServiceCatalogService;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Support\Flash;

final class ServiceController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly ServiceCatalogService $services,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        return $this->adminView('admin/services/index', $request, [
            'title' => 'Quản lý dịch vụ',
            'services' => $this->services->all(),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->adminView('admin/services/form', $request, [
            'title' => 'Thêm dịch vụ',
            'service' => null,
        ]);
    }

    public function store(Request $request): Response
    {
        $service = $this->services->create($this->withUpload($request, $request->all(), 'services'), $request);
        Flash::put('success', 'Đã thêm dịch vụ ' . $service['service_name']);
        return $this->redirect('/admin/services');
    }

    public function edit(Request $request, string $id): Response
    {
        return $this->adminView('admin/services/form', $request, [
            'title' => 'Sửa dịch vụ',
            'service' => $this->services->find($this->intParam($id)),
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $service = $this->services->update(
            $this->intParam($id),
            $this->withUpload($request, $request->all(), 'services'),
            $request,
        );
        Flash::put('success', 'Đã cập nhật dịch vụ ' . $service['service_name']);
        return $this->redirect('/admin/services');
    }

    public function destroy(Request $request, string $id): Response
    {
        $id = $this->intParam($id);
        $this->services->delete($id, $request);
        Flash::put('success', 'Đã xóa dịch vụ #' . $id);
        return $this->redirect('/admin/services');
    }
}
