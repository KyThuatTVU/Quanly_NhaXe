<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\PostService;
use App\Domain\Services\SettingService;
use App\Support\Paginator;

final class PostController extends WebController
{
    public function __construct(
        SettingService $settings,
        private readonly PostService $posts,
    ) {
        parent::__construct($settings);
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int) $request->query('page', 1));
        $category = (string) $request->query('category', '');
        $search = (string) $request->query('search', '');
        $result = $this->posts->paginate([
            'status' => 'published',
            'category' => $category !== '' ? $category : null,
            'search' => $search !== '' ? $search : null,
        ], $page, 9);

        return $this->view('web/posts/index', [
            'title' => 'Bài viết',
            'posts' => $result,
            'categories' => $this->posts->categories(),
            'filters' => ['category' => $category, 'search' => $search],
            'pagination' => Paginator::window($result, url('/bai-viet'), [
                'category' => $category,
                'search' => $search,
            ]),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        return $this->view('web/posts/show', [
            'title' => 'Bài viết',
            'post' => $this->posts->findBySlug($slug),
            'related' => $this->posts->latest(3),
        ]);
    }
}
