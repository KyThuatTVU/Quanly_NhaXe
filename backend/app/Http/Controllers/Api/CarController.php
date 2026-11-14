<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\CarService;

final class CarController extends ApiController
{
    public function __construct(private readonly CarService $cars)
    {
    }

    public function index(Request $request): Response
    {
        return Response::ok($this->cars->all([
            'vehicle_type_id' => $request->query('vehicle_type_id'),
            'search' => $request->query('search'),
            'status' => 'available',
        ]));
    }

    public function adminIndex(Request $request): Response
    {
        return Response::ok($this->cars->all([
            'vehicle_type_id' => $request->query('vehicle_type_id'),
            'search' => $request->query('search'),
            'status' => $request->query('status'),
        ]));
    }

    public function show(string $id): Response
    {
        return Response::ok($this->cars->find($this->id($id)));
    }

    public function store(Request $request): Response
    {
        return Response::created($this->cars->create($request->all(), $request), 'Thêm xe thành công');
    }

    public function update(Request $request, string $id): Response
    {
        return Response::ok($this->cars->update($this->id($id), $request->all(), $request), 'Cập nhật xe thành công');
    }

    public function destroy(Request $request, string $id): Response
    {
        $this->cars->delete($this->id($id), $request);
        return Response::ok(null, 'Xóa xe thành công');
    }
}
