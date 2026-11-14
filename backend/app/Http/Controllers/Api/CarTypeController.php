<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\CarTypeService;

final class CarTypeController extends ApiController
{
    public function __construct(private readonly CarTypeService $types)
    {
    }

    public function index(): Response
    {
        return Response::ok($this->types->all(true));
    }

    public function adminIndex(): Response
    {
        return Response::ok($this->types->all(false));
    }

    public function show(string $id): Response
    {
        return Response::ok($this->types->find($this->id($id)));
    }

    public function store(Request $request): Response
    {
        return Response::created($this->types->create($request->all(), $request), 'Thêm loại xe thành công');
    }

    public function update(Request $request, string $id): Response
    {
        return Response::ok($this->types->update($this->id($id), $request->all(), $request), 'Cập nhật loại xe thành công');
    }

    public function destroy(Request $request, string $id): Response
    {
        $this->types->delete($this->id($id), $request);
        return Response::ok(null, 'Xóa loại xe thành công');
    }
}
