<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\PostService;

final class PostController extends ApiController
{
    public function __construct(private readonly PostService $posts)
    {
    }

    public function index(Request $request): Response
    {
        $filters = [
            'status' => 'published',
            'category' => $request->query('category'),
            'search' => $request->query('search'),
        ];
        return Response::ok($this->posts->paginate($filters, $this->page($request), $this->perPage($request, 9)));
    }

    public function latest(Request $request): Response
    {
        return Response::ok($this->posts->latest(
            (int) $request->query('limit', 6),
            $request->query('category') !== null ? (string) $request->query('category') : null,
        ));
    }

    public function show(string $slug): Response
    {
        return Response::ok($this->posts->findBySlug($slug));
    }

    public function categories(): Response
    {
        return Response::ok($this->posts->categories());
    }

    public function adminIndex(Request $request): Response
    {
        $filters = [
            'status' => $request->query('status'),
            'category' => $request->query('category'),
            'search' => $request->query('search'),
        ];
        return Response::ok($this->posts->paginate($filters, $this->page($request), $this->perPage($request)));
    }

    public function adminShow(string $id): Response
    {
        return Response::ok($this->posts->find($this->id($id)));
    }

    public function store(Request $request): Response
    {
        return Response::created($this->posts->create($request->all(), $request), 'Thêm bài viết thành công');
    }

    public function update(Request $request, string $id): Response
    {
        return Response::ok($this->posts->update($this->id($id), $request->all(), $request), 'Cập nhật bài viết thành công');
    }

    public function destroy(Request $request, string $id): Response
    {
        $this->posts->delete($this->id($id), $request);
        return Response::ok(null, 'Xóa bài viết thành công');
    }
}
