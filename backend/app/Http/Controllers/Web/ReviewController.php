<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\ReviewService;
use App\Domain\Services\SettingService;
use App\Support\Flash;

final class ReviewController extends WebController
{
    public function __construct(
        SettingService $settings,
        private readonly ReviewService $reviews,
    ) {
        parent::__construct($settings);
    }

    public function index(Request $request): Response
    {
        return $this->view('web/reviews/index', ['title' => 'Đánh giá']);
    }

    public function store(Request $request): Response
    {
        $this->reviews->create($request->all(), $request);
        Flash::put('success', 'Cảm ơn bạn đã gửi đánh giá');
        return $this->redirect('/danh-gia');
    }
}
