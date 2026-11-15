<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\PostService;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Support\Flash;
use App\Support\Paginator;

final class PostController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly PostService $posts,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        $filters = [
            'status' => (string) $request->query('status', ''),
            'category' => (string) $request->query('category', ''),
            'search' => (string) $request->query('search', ''),
        ];
        $posts = $this->posts->paginate($filters, $this->page($request), $this->perPage($request));

        return $this->adminView('admin/posts/index', $request, [
            'title' => 'Quản lý bài viết',
            'posts' => $posts,
            'filters' => $filters,
            'categories' => $this->posts->categories(),
            'pagination' => Paginator::window($posts, url('/admin/posts'), array_filter($filters, static fn ($v) => $v !== '')),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->adminView('admin/posts/form', $request, [
            'title' => 'Thêm bài viết',
            'post' => null,
            'categories' => $this->posts->categories(),
        ]);
    }

    public function store(Request $request): Response
    {
        $post = $this->posts->create($this->withUpload($request, $request->all(), 'posts'), $request);
        Flash::put('success', 'Đã thêm bài viết ' . $post['title']);
        return $this->redirect('/admin/posts');
    }

    public function edit(Request $request, string $id): Response
    {
        return $this->adminView('admin/posts/form', $request, [
            'title' => 'Sửa bài viết',
            'post' => $this->posts->find($this->intParam($id)),
            'categories' => $this->posts->categories(),
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $post = $this->posts->update(
            $this->intParam($id),
            $this->withUpload($request, $request->all(), 'posts'),
            $request,
        );
        Flash::put('success', 'Đã cập nhật bài viết ' . $post['title']);
        return $this->redirect('/admin/posts');
    }

    public function destroy(Request $request, string $id): Response
    {
        $id = $this->intParam($id);
        $this->posts->delete($id, $request);
        Flash::put('success', 'Đã xóa bài viết #' . $id);
        return $this->redirect('/admin/posts');
    }
}
