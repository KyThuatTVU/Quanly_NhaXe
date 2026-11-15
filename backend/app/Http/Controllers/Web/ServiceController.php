<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\ServiceCatalogService;
use App\Domain\Services\SettingService;

final class ServiceController extends WebController
{
    public function __construct(
        SettingService $settings,
        private readonly ServiceCatalogService $services,
    ) {
        parent::__construct($settings);
    }

    public function index(Request $request): Response
    {
        return $this->view('web/services/index', [
            'title' => 'Dịch vụ',
            'services' => $this->services->all(true),
        ]);
    }

    public function show(Request $request, string $id): Response
    {
        return $this->view('web/services/show', [
            'title' => 'Chi tiết dịch vụ',
            'service' => $this->services->find($this->intParam($id)),
        ]);
    }
}
