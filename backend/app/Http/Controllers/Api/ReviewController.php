<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\ReviewService;

final class ReviewController extends ApiController
{
    public function __construct(private readonly ReviewService $reviews)
    {
    }

    public function byVehicle(string $id): Response
    {
        return Response::ok($this->reviews->byVehicle($this->id($id)));
    }

    public function store(Request $request): Response
    {
        return Response::created(
            $this->reviews->create($request->all(), $request),
            'Cảm ơn bạn đã gửi đánh giá',
        );
    }

    public function adminIndex(Request $request): Response
    {
        $visible = $request->query('visible');
        $filter = $visible === null || $visible === '' ? null : (bool) (int) $visible;
        return Response::ok($this->reviews->paginate($filter, $this->page($request), $this->perPage($request)));
    }

    public function setVisible(Request $request, string $id): Response
    {
        if ($request->input('visible') === null) {
            throw new ValidationException(['visible' => 'Vui lòng chọn trạng thái hiển thị.']);
        }
        $visible = filter_var($request->input('visible'), FILTER_VALIDATE_BOOL);
        return Response::ok(
            $this->reviews->setVisible($this->id($id), $visible, $request),
            $visible ? 'Đã hiển thị đánh giá' : 'Đã ẩn đánh giá',
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        $this->reviews->delete($this->id($id), $request);
        return Response::ok(null, 'Xóa đánh giá thành công');
    }
}
