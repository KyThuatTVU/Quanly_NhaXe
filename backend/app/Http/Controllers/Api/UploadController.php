<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\UploadService;

final class UploadController extends ApiController
{
    public function __construct(private readonly UploadService $uploads)
    {
    }

    public function store(Request $request): Response
    {
        $file = $request->file('file');
        if ($file === null) {
            throw new ValidationException(['file' => 'Vui lòng chọn ảnh để tải lên.']);
        }
        $folder = (string) $request->input('folder', 'cars');
        $result = $this->uploads->store($file, $folder);

        return Response::created($result, 'Tải ảnh lên thành công');
    }
}
