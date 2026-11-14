<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Exceptions\NotFoundException;
use App\Core\Request;
use App\Domain\Exceptions\BusinessRuleException;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Repositories\PdoServiceRepository;
use App\Support\Str;
use App\Support\Validator;

final class ServiceCatalogService
{
    public function __construct(
        private readonly PdoServiceRepository $services,
        private readonly AuditLogger $audit,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(bool $activeOnly = false): array
    {
        return $this->services->all($activeOnly);
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $id): array
    {
        $service = $this->services->find($id);
        if ($service === null) {
            throw new NotFoundException('Không tìm thấy dịch vụ');
        }
        return $service;
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function create(array $input, Request $request): array
    {
        $data = Validator::validate($input, [
            'service_name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = $data['slug'] !== null && $data['slug'] !== ''
            ? Str::slug((string) $data['slug'])
            : Str::slug((string) $data['service_name']);

        $id = $this->services->create([
            'service_name' => $data['service_name'],
            'slug' => $this->uniqueSlug($slug),
            'description' => $data['description'],
            'price' => $data['price'] !== null ? (float) $data['price'] : null,
            'image' => $data['image'],
            'is_active' => isset($input['is_active']) ? (filter_var($input['is_active'], FILTER_VALIDATE_BOOL) ? 1 : 0) : 1,
            'display_order' => isset($data['display_order']) ? (int) $data['display_order'] : 0,
        ]);

        $this->audit->log($request, 'service_create', 'services', $id, 'Thêm dịch vụ ' . $data['service_name']);
        return $this->find($id);
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function update(int $id, array $input, Request $request): array
    {
        $current = $this->find($id);

        $rules = [
            'service_name' => 'sometimes|required|string|max:100',
            'slug' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:5000',
            'price' => 'sometimes|nullable|numeric|min:0',
            'image' => 'sometimes|nullable|string|max:255',
            'display_order' => 'sometimes|nullable|integer',
            'is_active' => 'sometimes|nullable|boolean',
        ];
        $data = Validator::validate(array_intersect_key($input, $rules), $rules);
        $data = array_intersect_key($data, $input);

        $payload = [];
        foreach (['service_name', 'description', 'image'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }
        if (array_key_exists('price', $data)) {
            $payload['price'] = $data['price'] !== null ? (float) $data['price'] : null;
        }
        if (array_key_exists('display_order', $data)) {
            $payload['display_order'] = (int) $data['display_order'];
        }
        if (array_key_exists('is_active', $input)) {
            $payload['is_active'] = filter_var($input['is_active'], FILTER_VALIDATE_BOOL) ? 1 : 0;
        }
        if (array_key_exists('slug', $data)) {
            $slug = $data['slug'] !== null && $data['slug'] !== ''
                ? Str::slug((string) $data['slug'])
                : Str::slug((string) ($data['service_name'] ?? $current['service_name']));
            $payload['slug'] = $this->uniqueSlug($slug, $id);
        }

        $this->services->update($id, $payload);
        $this->audit->log($request, 'service_update', 'services', $id, 'Cập nhật dịch vụ #' . $id);
        return $this->find($id);
    }

    public function delete(int $id, Request $request): void
    {
        $service = $this->find($id);
        if ($this->services->countBookings($id) > 0) {
            throw new BusinessRuleException('Dịch vụ đang được dùng trong đơn đặt, không thể xóa');
        }
        $this->services->delete($id);
        $this->audit->log($request, 'service_delete', 'services', $id, 'Xóa dịch vụ ' . $service['service_name']);
    }

    private function uniqueSlug(string $slug, ?int $exceptId = null): string
    {
        $base = $slug !== '' ? $slug : 'dich-vu';
        $candidate = $base;
        $suffix = 1;
        while ($this->services->existsSlug($candidate, $exceptId)) {
            $candidate = $base . '-' . $suffix++;
        }
        return $candidate;
    }
}
