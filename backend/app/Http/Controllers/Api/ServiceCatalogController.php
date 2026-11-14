<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\ServiceCatalogService;

final class ServiceCatalogController extends ApiController
{
    public function __construct(private readonly ServiceCatalogService $services)
    {
    }

    public function index(): Response
    {
        return Response::ok($this->services->all(true));
    }

    public function adminIndex(): Response
    {
        return Response::ok($this->services->all(false));
    }

    public function show(string $id): Response
    {
        return Response::ok($this->services->find($this->id($id)));
    }

    public function store(Request $request): Response
    {
        return Response::created($this->services->create($request->all(), $request), 'Thêm dịch vụ thành công');
    }

    public function update(Request $request, string $id): Response
    {
        return Response::ok($this->services->update($this->id($id), $request->all(), $request), 'Cập nhật dịch vụ thành công');
    }

    public function destroy(Request $request, string $id): Response
    {
        $this->services->delete($this->id($id), $request);
        return Response::ok(null, 'Xóa dịch vụ thành công');
    }
}
