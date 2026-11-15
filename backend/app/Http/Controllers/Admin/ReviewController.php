<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\ReviewService;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Support\Flash;
use App\Support\Paginator;

final class ReviewController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly ReviewService $reviews,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        $visibleParam = $request->query('visible', '');
        $visible = $visibleParam === '' ? null : (bool) (int) $visibleParam;
        $reviews = $this->reviews->paginate($visible, $this->page($request), $this->perPage($request));

        return $this->adminView('admin/reviews/index', $request, [
            'title' => 'Quản lý đánh giá',
            'reviews' => $reviews,
            'visible' => $visibleParam,
            'pagination' => Paginator::window(
                $reviews,
                url('/admin/reviews'),
                $visibleParam === '' ? [] : ['visible' => $visibleParam],
            ),
        ]);
    }

    public function setVisible(Request $request, string $id): Response
    {
        $visible = filter_var($request->input('visible'), FILTER_VALIDATE_BOOL);
        $this->reviews->setVisible($this->intParam($id), $visible, $request);
        Flash::put('success', $visible ? 'Đã hiển thị đánh giá' : 'Đã ẩn đánh giá');
        return $this->back($request, '/admin/reviews');
    }

    public function destroy(Request $request, string $id): Response
    {
        $id = $this->intParam($id);
        $this->reviews->delete($id, $request);
        Flash::put('success', 'Đã xóa đánh giá #' . $id);
        return $this->redirect('/admin/reviews');
    }
}
