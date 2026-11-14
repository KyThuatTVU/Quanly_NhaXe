<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Config;
use App\Core\Exceptions\NotFoundException;
use App\Core\Response;

final class MediaController extends ApiController
{
    public function show(string $folder, string $filename): Response
    {
        $folders = (array) Config::get('upload.folders', []);
        if (!in_array($folder, $folders, true) || preg_match('/^[A-Za-z0-9._-]+$/', $filename) !== 1) {
            throw new NotFoundException('Không tìm thấy tệp');
        }

        $dir = rtrim((string) Config::get('app.base_path'), '/\\')
            . '/' . trim((string) Config::get('upload.dir', 'storage/uploads'), '/\\')
            . '/' . $folder;
        $path = realpath($dir . '/' . $filename);
        $base = realpath($dir);
        if ($path === false || $base === false || !str_starts_with($path, $base) || !is_file($path)) {
            throw new NotFoundException('Không tìm thấy tệp');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path) ?: 'application/octet-stream';
        $allowed = array_keys((array) Config::get('upload.mime', []));
        if (!in_array($mime, $allowed, true)) {
            throw new NotFoundException('Không tìm thấy tệp');
        }

        return Response::raw((string) file_get_contents($path), 200, [
            'Content-Type' => $mime,
            'Content-Length' => (string) filesize($path),
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
