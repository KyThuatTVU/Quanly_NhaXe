<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Http\Controllers\Web\WebController;

abstract class AdminController extends WebController
{
    public function __construct(SettingService $settings, protected readonly UploadService $uploads)
    {
        parent::__construct($settings);
    }

    /**
     * Gắn đường dẫn ảnh tải lên (nếu có) vào dữ liệu biểu mẫu.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function withUpload(Request $request, array $data, string $folder): array
    {
        $file = $request->file('image_file');
        if ($file !== null) {
            $data['image'] = $this->uploads->store($file, $folder)['path'];
        } elseif (array_key_exists('image', $data) && trim((string) $data['image']) === '') {
            unset($data['image']);
        }
        return $data;
    }

    protected function page(Request $request): int
    {
        return max(1, (int) $request->query('page', 1));
    }

    protected function perPage(Request $request, int $default = 15): int
    {
        $value = (int) $request->query('per_page', $default);
        return in_array($value, [10, 15, 20, 30, 50], true) ? $value : $default;
    }
}
