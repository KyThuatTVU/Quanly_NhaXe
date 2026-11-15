<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Exceptions\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Domain\Services\AuthService;
use App\Domain\Services\SettingService;
use App\Http\Controllers\Web\WebController;
use App\Infrastructure\Persistence\Repositories\PdoSessionRepository;
use App\Support\Flash;
use App\Support\Validator;

final class AuthController extends WebController
{
    public function __construct(
        SettingService $settings,
        private readonly AuthService $auth,
        private readonly PdoSessionRepository $sessions,
    ) {
        parent::__construct($settings);
    }

    public function showLogin(Request $request): Response
    {
        $token = Session::get('auth_token');
        if (is_string($token) && $token !== '' && $this->sessions->findActive($token) !== null) {
            return $this->redirect('/admin');
        }
        return $this->view('web/auth/login', ['title' => 'Đăng nhập quản trị'], 'layouts/auth');
    }

    public function login(Request $request): Response
    {
        try {
            $data = Validator::validate($request->all(), [
                'login' => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ]);
            $result = $this->auth->login((string) $data['login'], (string) $data['password'], $request);
        } catch (HttpException $exception) {
            Flash::put('error', $exception->getMessage());
            Flash::put('old', ['login' => (string) $request->input('login', '')]);
            return $this->redirect('/admin/login');
        }

        Session::regenerate();
        Session::set('auth_token', $result['token']);
        Session::set('auth_user_id', (int) $result['user']['user_id']);

        $intended = Session::get('_intended');
        Session::forget('_intended');
        $target = is_string($intended) && $intended !== '' ? $intended : '/admin';
        return $this->redirect($target);
    }

    public function logout(Request $request): Response
    {
        $token = $request->attribute('auth_token');
        $token = is_string($token) ? $token : Session::get('auth_token');
        $this->auth->logout(is_string($token) ? $token : null, $request);
        Session::forget('auth_token');
        Session::forget('auth_user_id');
        Flash::put('success', 'Đã đăng xuất');
        return $this->redirect('/admin/login');
    }
}
