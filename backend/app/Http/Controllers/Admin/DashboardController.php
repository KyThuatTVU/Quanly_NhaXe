<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\SettingService;
use App\Domain\Services\StatisticService;
use App\Domain\Services\UploadService;

final class DashboardController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly StatisticService $statistics,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        $month = $this->statistics->grouped(date('Y-m-01'), date('Y-m-t'), 'day');
        $summary = $this->statistics->summary(date('Y-m-01'), date('Y-m-t'));

        return $this->adminView('admin/dashboard/index', $request, [
            'title' => 'Tổng quan',
            'stats' => $this->statistics->dashboard(),
            'summary' => $summary['summary'],
            'chart' => $month['items'],
            'monthLabel' => date('m/Y'),
        ]);
    }
}
