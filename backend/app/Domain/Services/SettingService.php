<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Request;
use App\Domain\Exceptions\BusinessRuleException;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Repositories\PdoSettingRepository;
use App\Support\Validator;

final class SettingService
{
    public function __construct(
        private readonly PdoSettingRepository $settings,
        private readonly AuditLogger $audit,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function get(): array
    {
        return $this->settings->get() ?? [
            'id' => 1,
            'company_name' => '',
            'address' => null,
            'phone' => '',
            'hotline' => null,
            'zalo' => '',
            'facebook' => null,
            'email' => null,
            'working_hours' => null,
            'map_location' => null,
            'description' => null,
        ];
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function update(array $input, Request $request): array
    {
        $rules = [
            'company_name' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'hotline' => 'nullable|string|max:20',
            'zalo' => 'nullable|string|max:20',
            'facebook' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'working_hours' => 'nullable|string|max:100',
            'map_location' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:5000',
        ];
        $data = Validator::validate(array_intersect_key($input, $rules), $rules);
        $data = array_intersect_key($data, $input);

        if (array_key_exists('phone', $data) && $data['phone'] !== null && !Validator::isPhone((string) $data['phone'])) {
            throw new BusinessRuleException('Số điện thoại công ty không đúng định dạng');
        }
        if (array_key_exists('hotline', $data) && $data['hotline'] !== null && !Validator::isPhone((string) $data['hotline'])) {
            throw new BusinessRuleException('Hotline không đúng định dạng');
        }

        $this->settings->update($data);
        $this->audit->log($request, 'setting_update', 'settings', 1, 'Cập nhật thông tin website');
        return $this->get();
    }
}
