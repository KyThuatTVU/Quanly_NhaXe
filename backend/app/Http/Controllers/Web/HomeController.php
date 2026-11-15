<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\CarService;
use App\Domain\Services\CarTypeService;
use App\Domain\Services\PostService;
use App\Domain\Services\ServiceCatalogService;
use App\Domain\Services\SettingService;

final class HomeController extends WebController
{
    public function __construct(
        SettingService $settings,
        private readonly CarTypeService $types,
        private readonly CarService $cars,
        private readonly ServiceCatalogService $services,
        private readonly PostService $posts,
    ) {
        parent::__construct($settings);
    }

    public function index(Request $request): Response
    {
        return $this->view('web/home/index', [
            'title' => 'Thuê xe hợp đồng',
            'types' => $this->types->all(true),
            'cars' => array_slice($this->cars->all(['status' => 'available']), 0, 6),
            'services' => array_slice($this->services->all(true), 0, 4),
            'posts' => $this->posts->latest(3),
        ]);
    }
}
