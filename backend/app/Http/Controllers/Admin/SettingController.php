<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Support\Flash;

final class SettingController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        return $this->adminView('admin/settings/index', $request, [
            'title' => 'Cài đặt website',
            'settings' => $this->settings->get(),
        ]);
    }

    public function update(Request $request): Response
    {
        $this->settings->update($request->all(), $request);
        Flash::put('success', 'Đã lưu thông tin website');
        return $this->redirect('/admin/settings');
    }
}
