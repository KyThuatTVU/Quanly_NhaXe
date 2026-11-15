<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Config;
use App\Core\Exceptions\ValidationException;
use App\Domain\Exceptions\BusinessRuleException;
use App\Support\Str;

final class UploadService
{
    /**
     * @param array<string, mixed> $file
     * @return array{path: string, url: string, mime: string, size: int}
     */
    public function store(array $file, string $folder): array
    {
        $folders = (array) Config::get('upload.folders', []);
        if (!in_array($folder, $folders, true)) {
            throw new BusinessRuleException('Thư mục tải lên không hợp lệ');
        }
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new ValidationException(['file' => 'Tải ảnh thất bại, vui lòng thử lại']);
        }
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > (int) Config::get('upload.max_size', 5242880)) {
            $maxMb = round((int) Config::get('upload.max_size', 5242880) / 1048576, 1);
            throw new ValidationException(['file' => "Ảnh vượt quá dung lượng cho phép ({$maxMb}MB)"]);
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new ValidationException(['file' => 'Tệp tải lên không hợp lệ']);
        }

        $mimeMap = (array) Config::get('upload.mime', []);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp) ?: '';
        if (!isset($mimeMap[$mime])) {
            throw new ValidationException(['file' => 'Chỉ chấp nhận ảnh JPG, PNG, GIF hoặc WEBP']);
        }

        $dir = rtrim((string) Config::get('app.base_path'), '/\\')
            . '/' . trim((string) Config::get('upload.dir', 'storage/uploads'), '/\\')
            . '/' . $folder;
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new BusinessRuleException('Không tạo được thư mục lưu ảnh');
        }

        $filename = date('Ymd') . '-' . Str::token(8) . '.' . $mimeMap[$mime];
        $destination = $dir . '/' . $filename;
        if (!move_uploaded_file($tmp, $destination)) {
            throw new BusinessRuleException('Không lưu được ảnh tải lên');
        }

        $path = 'uploads/' . $folder . '/' . $filename;
        return [
            'path' => $path,
            'url' => url($path),
            'mime' => $mime,
            'size' => $size,
        ];
    }
}
