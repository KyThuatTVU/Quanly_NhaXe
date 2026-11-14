<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Exceptions\NotFoundException;
use App\Core\Request;
use App\Domain\Exceptions\BusinessRuleException;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Repositories\PdoCarTypeRepository;
use App\Support\Str;
use App\Support\Validator;

final class CarTypeService
{
    public function __construct(
        private readonly PdoCarTypeRepository $types,
        private readonly AuditLogger $audit,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(bool $activeOnly = false): array
    {
        return $this->types->all($activeOnly);
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $id): array
    {
        $type = $this->types->find($id);
        if ($type === null) {
            throw new NotFoundException('Không tìm thấy loại xe');
        }
        return $type;
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function create(array $input, Request $request): array
    {
        $data = Validator::validate($input, [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'seats' => 'required|integer|min:1|max:100',
            'seats_min' => 'nullable|integer|min:1|max:100',
            'seats_max' => 'nullable|integer|min:1|max:100',
            'description' => 'nullable|string|max:5000',
            'icon' => 'nullable|string|max:100',
            'image_url' => 'nullable|string|max:500',
            'price_per_day' => 'nullable|numeric|min:0',
            'price_per_km' => 'nullable|numeric|min:0',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $this->assertSeatRange($data);

        $slug = $data['slug'] !== null && $data['slug'] !== ''
            ? Str::slug((string) $data['slug'])
            : Str::slug((string) $data['name']);
        $slug = $this->uniqueSlug($slug);

        $id = $this->types->create([
            'name' => $data['name'],
            'slug' => $slug,
            'seats' => (int) $data['seats'],
            'seats_min' => $data['seats_min'] !== null ? (int) $data['seats_min'] : null,
            'seats_max' => $data['seats_max'] !== null ? (int) $data['seats_max'] : null,
            'description' => $data['description'],
            'icon' => $data['icon'],
            'image_url' => $data['image_url'],
            'price_per_day' => $data['price_per_day'] !== null ? (float) $data['price_per_day'] : null,
            'price_per_km' => $data['price_per_km'] !== null ? (float) $data['price_per_km'] : null,
            'features' => $this->encodeFeatures($input['features'] ?? null),
            'is_active' => isset($input['is_active']) ? (filter_var($input['is_active'], FILTER_VALIDATE_BOOL) ? 1 : 0) : 1,
            'display_order' => isset($data['display_order']) ? (int) $data['display_order'] : 0,
        ]);

        $this->audit->log($request, 'car_type_create', 'vehicle_types', $id, 'Thêm loại xe ' . $data['name']);
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
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255',
            'seats' => 'sometimes|required|integer|min:1|max:100',
            'seats_min' => 'nullable|integer|min:1|max:100',
            'seats_max' => 'nullable|integer|min:1|max:100',
            'description' => 'nullable|string|max:5000',
            'icon' => 'nullable|string|max:100',
            'image_url' => 'nullable|string|max:500',
            'price_per_day' => 'nullable|numeric|min:0',
            'price_per_km' => 'nullable|numeric|min:0',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
        $data = Validator::validate(array_intersect_key($input, $rules), $rules);
        $data = array_intersect_key($data, $input);

        $current = $this->find($id);
        $payload = [];
        foreach (['name', 'seats', 'seats_min', 'seats_max', 'description', 'icon', 'image_url', 'display_order'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }
        foreach (['seats', 'seats_min', 'seats_max', 'display_order'] as $field) {
            if (array_key_exists($field, $payload) && $payload[$field] !== null) {
                $payload[$field] = (int) $payload[$field];
            }
        }
        foreach (['price_per_day', 'price_per_km'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field] !== null ? (float) $data[$field] : null;
            }
        }
        if (array_key_exists('is_active', $input)) {
            $payload['is_active'] = filter_var($input['is_active'], FILTER_VALIDATE_BOOL) ? 1 : 0;
        }
        if (array_key_exists('features', $input)) {
            $payload['features'] = $this->encodeFeatures($input['features']);
        }
        if (array_key_exists('slug', $data)) {
            $slug = $data['slug'] !== null && $data['slug'] !== ''
                ? Str::slug((string) $data['slug'])
                : Str::slug((string) ($data['name'] ?? $current['name']));
            $payload['slug'] = $this->uniqueSlug($slug, $id);
        }

        $merged = array_merge($current, $payload);
        $this->assertSeatRange($merged);

        $this->types->update($id, $payload);
        $this->audit->log($request, 'car_type_update', 'vehicle_types', $id, 'Cập nhật loại xe #' . $id);
        return $this->find($id);
    }

    public function delete(int $id, Request $request): void
    {
        $type = $this->find($id);
        if ($this->types->countVehicles($id) > 0) {
            throw new BusinessRuleException('Loại xe đang được sử dụng, không thể xóa');
        }
        $this->types->delete($id);
        $this->audit->log($request, 'car_type_delete', 'vehicle_types', $id, 'Xóa loại xe ' . $type['name']);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function assertSeatRange(array $data): void
    {
        $min = isset($data['seats_min']) && $data['seats_min'] !== null ? (int) $data['seats_min'] : null;
        $max = isset($data['seats_max']) && $data['seats_max'] !== null ? (int) $data['seats_max'] : null;
        if ($min !== null && $max !== null && $max < $min) {
            throw new BusinessRuleException('Số chỗ tối đa phải lớn hơn hoặc bằng số chỗ tối thiểu');
        }
    }

    private function uniqueSlug(string $slug, ?int $exceptId = null): string
    {
        $base = $slug !== '' ? $slug : 'loai-xe';
        $candidate = $base;
        $suffix = 1;
        while ($this->types->existsSlug($candidate, $exceptId)) {
            $candidate = $base . '-' . $suffix++;
        }
        return $candidate;
    }

    private function encodeFeatures(mixed $features): ?string
    {
        if ($features === null || $features === '') {
            return null;
        }
        if (is_string($features)) {
            $features = array_values(array_filter(array_map('trim', explode(',', $features))));
        }
        if (!is_array($features)) {
            return null;
        }
        return json_encode($features, JSON_UNESCAPED_UNICODE) ?: null;
    }
}
