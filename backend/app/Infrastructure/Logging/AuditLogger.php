<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Core\Request;
use App\Infrastructure\Persistence\Repositories\PdoAuditLogRepository;

final class AuditLogger
{
    public function __construct(private readonly PdoAuditLogRepository $logs)
    {
    }

    public function log(
        Request $request,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
    ): void {
        $this->logs->add(
            $request->userId(),
            $action,
            $entityType,
            $entityId,
            $description,
            $request->ip(),
            (string) $request->header('user-agent', ''),
        );
    }
}
