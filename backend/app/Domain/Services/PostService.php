<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Exceptions\NotFoundException;
use App\Core\Request;
use App\Domain\Exceptions\BusinessRuleException;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Repositories\PdoPostRepository;
use App\Support\Str;
use App\Support\Validator;

final class PostService
{
    private const STATUSES = ['draft', 'published'];

    public function __construct(
        private readonly PdoPostRepository $posts,
        private readonly AuditLogger $audit,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function latest(int $limit = 6, ?string $category = null): array
    {
        return $this->posts->published($limit, $category);
    }

    /**
     * @return array<string, mixed>
     */
    public function findBySlug(string $slug): array
    {
        $post = $this->posts->findBySlug($slug);
        if ($post === null || $post['status'] !== 'published') {
            throw new NotFoundException('Không tìm thấy bài viết');
        }
        return $post;
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters, int $page, int $perPage): array
    {
        if (!empty($filters['status']) && !in_array($filters['status'], self::STATUSES, true)) {
            throw new BusinessRuleException('Trạng thái bài viết không hợp lệ');
        }
        return $this->posts->paginate($filters, $page, $perPage);
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $id): array
    {
        $post = $this->posts->find($id);
        if ($post === null) {
            throw new NotFoundException('Không tìm thấy bài viết');
        }
        return $post;
    }

    /**
     * @return array<int, array{name: string, total: int}>
     */
    public function categories(): array
    {
        return $this->posts->categories();
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function create(array $input, Request $request): array
    {
        $data = Validator::validate($input, [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:50',
            'status' => 'nullable|in:draft,published',
        ]);

        $slug = $data['slug'] !== null && $data['slug'] !== ''
            ? Str::slug((string) $data['slug'])
            : Str::slug((string) $data['title']);

        $id = $this->posts->create([
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($slug),
            'content' => $data['content'],
            'image' => $data['image'],
            'category' => $data['category'],
            'status' => $data['status'] ?? 'published',
            'created_by' => $request->userId(),
        ]);

        $this->audit->log($request, 'post_create', 'posts', $id, 'Thêm bài viết ' . $data['title']);
        return $this->find($id);
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function update(int $id, array $input, Request $request): array
    {
        $current = $this->find($id);

        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255',
            'content' => 'sometimes|nullable|string',
            'image' => 'sometimes|nullable|string|max:255',
            'category' => 'sometimes|nullable|string|max:50',
            'status' => 'sometimes|required|in:draft,published',
        ];
        $data = Validator::validate(array_intersect_key($input, $rules), $rules);
        $data = array_intersect_key($data, $input);
        $payload = array_intersect_key($data, $rules);

        if (array_key_exists('slug', $data)) {
            $slug = $data['slug'] !== null && $data['slug'] !== ''
                ? Str::slug((string) $data['slug'])
                : Str::slug((string) ($data['title'] ?? $current['title']));
            $payload['slug'] = $this->uniqueSlug($slug, $id);
        }

        $this->posts->update($id, $payload);
        $this->audit->log($request, 'post_update', 'posts', $id, 'Cập nhật bài viết #' . $id);
        return $this->find($id);
    }

    public function delete(int $id, Request $request): void
    {
        $post = $this->find($id);
        $this->posts->delete($id);
        $this->audit->log($request, 'post_delete', 'posts', $id, 'Xóa bài viết ' . $post['title']);
    }

    private function uniqueSlug(string $slug, ?int $exceptId = null): string
    {
        $base = $slug !== '' ? $slug : 'bai-viet';
        $candidate = $base;
        $suffix = 1;
        while ($this->posts->existsSlug($candidate, $exceptId)) {
            $candidate = $base . '-' . $suffix++;
        }
        return $candidate;
    }
}
