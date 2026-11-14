<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\SettingService;

final class SettingController extends ApiController
{
    public function __construct(private readonly SettingService $settings)
    {
    }

    public function show(): Response
    {
        return Response::ok($this->settings->get());
    }

    public function update(Request $request): Response
    {
        return Response::ok($this->settings->update($request->all(), $request), 'Cập nhật thông tin website thành công');
    }
}
