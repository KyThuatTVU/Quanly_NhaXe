<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Infrastructure\Persistence\Repositories\PdoAuditLogRepository;

final class AuditLogController extends ApiController
{
    public function __construct(private readonly PdoAuditLogRepository $logs)
    {
    }

    public function index(Request $request): Response
    {
        return Response::ok($this->logs->paginate($this->page($request), $this->perPage($request, 30)));
    }
}
