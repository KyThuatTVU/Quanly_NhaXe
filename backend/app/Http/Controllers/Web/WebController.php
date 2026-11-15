<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Config;
use App\Core\Exceptions\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Domain\Exceptions\BusinessRuleException;
use App\Domain\Services\SettingService;
use App\Support\Flash;

abstract class WebController
{
    private ?array $siteCache = null;

    public function __construct(protected readonly SettingService $settings)
    {
        Flash::rotate();
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function view(string $view, array $data = [], string $layout = 'layouts/app'): Response
    {
        $user = null;
        return View::renderResponse(
            $view,
            array_merge(['site' => $this->site(), 'currentUser' => $user], $data),
            $layout,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function adminView(string $view, Request $request, array $data = []): Response
    {
        return View::renderResponse(
            $view,
            array_merge(['site' => $this->site(), 'currentUser' => $request->user()], $data),
            'layouts/admin',
        );
    }

    protected function redirect(string $path, int $status = 302): Response
    {
        return Response::redirect(url($path), $status);
    }

    protected function back(Request $request, string $fallback = '/'): Response
    {
        $referer = (string) $request->header('referer', '');
        if ($referer !== '' && str_starts_with($referer, base_url())) {
            return Response::redirect($referer);
        }
        return $this->redirect($fallback);
    }

    /**
     * @param array<string, string> $errors
     */
    protected function validationRedirect(Request $request, array $errors, string $fallback = '/'): Response
    {
        Flash::put('errors', $errors);
        Flash::put('old', $request->all());
        return $this->back($request, $fallback);
    }

    protected function withError(Request $request, string $message, string $fallback = '/'): Response
    {
        Flash::put('error', $message);
        Flash::put('old', $request->all());
        return $this->back($request, $fallback);
    }

    /**
     * @return array<string, mixed>
     */
    protected function site(): array
    {
        if ($this->siteCache === null) {
            $site = $this->settings->get();
            $name = trim((string) ($site['company_name'] ?? ''));
            $site['name'] = $name !== '' ? $name : (string) Config::get('app.name', 'Nhà xe');
            $this->siteCache = $site;
        }
        return $this->siteCache;
    }

    protected function notFound(string $message = 'Không tìm thấy trang'): never
    {
        throw new HttpException($message, 404);
    }

    protected function businessError(string $message): never
    {
        throw new BusinessRuleException($message);
    }

    protected function intParam(string $value): int
    {
        $id = (int) $value;
        if ($id <= 0) {
            $this->notFound('Không tìm thấy dữ liệu');
        }
        return $id;
    }
}
