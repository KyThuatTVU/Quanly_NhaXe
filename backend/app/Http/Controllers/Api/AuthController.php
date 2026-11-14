<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Domain\Exceptions\BusinessRuleException;
use App\Domain\Services\AuthService;
use App\Infrastructure\Persistence\Repositories\PdoSessionRepository;
use App\Support\Validator;

final class AuthController extends ApiController
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly PdoSessionRepository $sessions,
    ) {
    }

    public function csrf(): Response
    {
        return Response::ok(['csrf_token' => Session::csrfToken()]);
    }

    public function login(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'login' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $result = $this->auth->login((string) $data['login'], (string) $data['password'], $request);

        Session::regenerate();
        Session::set('auth_token', $result['token']);
        Session::set('auth_user_id', $result['user']['user_id']);

        return Response::ok($result, 'Đăng nhập thành công');
    }

    public function logout(Request $request): Response
    {
        $token = $request->attribute('auth_token');
        $this->auth->logout(is_string($token) ? $token : null, $request);
        Session::forget('auth_token');
        Session::forget('auth_user_id');

        return Response::ok(null, 'Đã đăng xuất');
    }

    public function me(Request $request): Response
    {
        $user = $request->user();
        if ($user === null) {
            throw new \App\Core\Exceptions\UnauthorizedException();
        }
        return Response::ok($this->auth->publicUser($user) + ['csrf_token' => Session::csrfToken()]);
    }

    public function changePassword(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|max:255',
        ]);
        $user = $request->user();
        if ($user === null) {
            throw new \App\Core\Exceptions\UnauthorizedException();
        }
        if ((string) $data['current_password'] === (string) $data['new_password']) {
            throw new BusinessRuleException('Mật khẩu mới phải khác mật khẩu hiện tại');
        }

        $this->auth->changePassword($user, (string) $data['current_password'], (string) $data['new_password']);
        $this->sessions->deactivateAllForUser((int) $user['user_id']);
        Session::forget('auth_token');

        return Response::ok(null, 'Đổi mật khẩu thành công, vui lòng đăng nhập lại');
    }
}
