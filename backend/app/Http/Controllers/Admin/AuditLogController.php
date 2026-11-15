<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Infrastructure\Persistence\Repositories\PdoAuditLogRepository;
use App\Support\Paginator;

final class AuditLogController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly PdoAuditLogRepository $logs,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        $logs = $this->logs->paginate($this->page($request), 30);

        return $this->adminView('admin/audit-logs/index', $request, [
            'title' => 'Nhật ký hệ thống',
            'logs' => $logs,
            'pagination' => Paginator::window($logs, url('/admin/audit-logs')),
        ]);
    }
}
