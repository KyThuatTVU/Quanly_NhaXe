<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Exceptions\NotFoundException;
use App\Core\Request;
use App\Domain\Enums\CarStatus;
use App\Domain\Exceptions\BusinessRuleException;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Repositories\PdoCarRepository;
use App\Infrastructure\Persistence\Repositories\PdoCarTypeRepository;
use App\Support\Validator;

final class CarService
{
    public function __construct(
        private readonly PdoCarRepository $cars,
        private readonly PdoCarTypeRepository $types,
        private readonly AuditLogger $audit,
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function all(array $filters = []): array
    {
        if (!empty($filters['status']) && !in_array($filters['status'], CarStatus::values(), true)) {
            throw new BusinessRuleException('Trạng thái xe không hợp lệ');
        }
        return $this->cars->all($filters);
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $id): array
    {
        $car = $this->cars->find($id);
        if ($car === null) {
            throw new NotFoundException('Không tìm thấy xe');
        }
        return $car;
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function create(array $input, Request $request): array
    {
        $data = Validator::validate($input, [
            'name' => 'required|string|max:100',
            'vehicle_type_id' => 'required|integer',
            'type' => 'nullable|string|max:50',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price_per_km' => 'nullable|numeric|min:0',
            'price_per_day' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:available,unavailable',
            'driver_info' => 'nullable|string|max:2000',
            'insurance_info' => 'nullable|string|max:2000',
        ]);

        $type = $this->types->find((int) $data['vehicle_type_id']);
        if ($type === null) {
            throw new NotFoundException('Không tìm thấy loại xe');
        }

        $id = $this->cars->create([
            'vehicle_type_id' => (int) $data['vehicle_type_id'],
            'name' => $data['name'],
            'type' => $data['type'] !== null && $data['type'] !== '' ? $data['type'] : $type['name'],
            'image' => $data['image'],
            'description' => $data['description'],
            'price_per_km' => $data['price_per_km'] !== null ? (float) $data['price_per_km'] : null,
            'price_per_day' => $data['price_per_day'] !== null ? (float) $data['price_per_day'] : null,
            'status' => $data['status'] ?? CarStatus::Available->value,
            'driver_info' => $data['driver_info'],
            'rating' => 0,
            'insurance_info' => $data['insurance_info'],
        ]);

        $this->audit->log($request, 'car_create', 'vehicles', $id, 'Thêm xe ' . $data['name']);
        return $this->find($id);
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function update(int $id, array $input, Request $request): array
    {
        $this->find($id);

        $rules = [
            'name' => 'sometimes|required|string|max:100',
            'vehicle_type_id' => 'sometimes|nullable|integer',
            'type' => 'sometimes|nullable|string|max:50',
            'image' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:5000',
            'price_per_km' => 'sometimes|nullable|numeric|min:0',
            'price_per_day' => 'sometimes|nullable|numeric|min:0',
            'status' => 'sometimes|required|in:available,unavailable',
            'driver_info' => 'nullable|string|max:2000',
            'insurance_info' => 'nullable|string|max:2000',
        ];
        $data = Validator::validate(array_intersect_key($input, $rules), $rules);
        $data = array_intersect_key($data, $input);

        if (array_key_exists('vehicle_type_id', $data) && $data['vehicle_type_id'] !== null) {
            if ($this->types->find((int) $data['vehicle_type_id']) === null) {
                throw new NotFoundException('Không tìm thấy loại xe');
            }
            $data['vehicle_type_id'] = (int) $data['vehicle_type_id'];
        }
        foreach (['price_per_km', 'price_per_day'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null) {
                $data[$field] = (float) $data[$field];
            }
        }

        $this->cars->update($id, $data);
        $this->audit->log($request, 'car_update', 'vehicles', $id, 'Cập nhật xe #' . $id);
        return $this->find($id);
    }

    public function delete(int $id, Request $request): void
    {
        $car = $this->find($id);
        if ($this->cars->countBookings($id) > 0) {
            throw new BusinessRuleException('Xe đang có đơn đặt, không thể xóa');
        }
        $this->cars->delete($id);
        $this->audit->log($request, 'car_delete', 'vehicles', $id, 'Xóa xe ' . $car['name']);
    }
}
