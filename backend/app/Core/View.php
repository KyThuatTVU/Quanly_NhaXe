<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Render PHP template trong app/Views. Layout nhận biến $content.
 */
final class View
{
    public function __construct(private readonly string $basePath = '')
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function renderResponse(string $view, array $data = [], ?string $layout = 'layouts/app'): Response
    {
        $instance = new self();
        $content = $instance->capture($view, $data);
        if ($layout !== null) {
            $content = $instance->capture($layout, array_merge($data, ['content' => $content]));
        }
        return Response::html($content);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function partial(string $view, array $data = []): void
    {
        echo $this->capture($view, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function capture(string $view, array $data = []): string
    {
        $__file = $this->resolve($view);
        extract($data, EXTR_SKIP);
        $view = $this;
        ob_start();
        try {
            require $__file;
            return (string) ob_get_clean();
        } catch (\Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }
    }

    private function resolve(string $view): string
    {
        if (preg_match('#^[A-Za-z0-9_\-/]+$#', $view) !== 1) {
            throw new RuntimeException('Tên view không hợp lệ: ' . $view);
        }
        $base = $this->basePath !== ''
            ? $this->basePath
            : rtrim((string) Config::get('app.base_path'), '/\\') . '/app/Views';
        $file = $base . '/' . trim($view, '/') . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('Không tìm thấy view: ' . $view);
        }
        return $file;
    }
}
