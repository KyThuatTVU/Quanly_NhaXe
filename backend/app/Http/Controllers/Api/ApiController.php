<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;

abstract class ApiController
{
    protected function page(Request $request): int
    {
        return max(1, (int) $request->query('page', 1));
    }

    protected function perPage(Request $request, int $default = 15): int
    {
        return max(1, min(100, (int) $request->query('per_page', $default)));
    }

    protected function id(string $value): int
    {
        $id = (int) $value;
        if ($id <= 0) {
            throw new \App\Core\Exceptions\NotFoundException('Không tìm thấy dữ liệu');
        }
        return $id;
    }
}
