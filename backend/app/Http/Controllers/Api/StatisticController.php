<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\StatisticService;

final class StatisticController extends ApiController
{
    public function __construct(private readonly StatisticService $statistics)
    {
    }

    public function dashboard(): Response
    {
        return Response::ok($this->statistics->dashboard());
    }

    public function grouped(Request $request): Response
    {
        return Response::ok($this->statistics->grouped(
            $request->query('from') !== null ? (string) $request->query('from') : null,
            $request->query('to') !== null ? (string) $request->query('to') : null,
            $request->query('group') !== null ? (string) $request->query('group') : null,
        ));
    }

    public function summary(Request $request): Response
    {
        return Response::ok($this->statistics->summary(
            $request->query('from') !== null ? (string) $request->query('from') : null,
            $request->query('to') !== null ? (string) $request->query('to') : null,
        ));
    }
}
