<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Domain\Exceptions\BusinessRuleException;
use App\Domain\Services\AccountService;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Infrastructure\Persistence\Repositories\PdoUserRepository;
use App\Support\Flash;
use App\Support\Validator;

final class ProfileController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly AccountService $accounts,
        private readonly PdoUserRepository $users,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        return $this->adminView('admin/profile/index', $request, [
            'title' => 'Thông tin cá nhân',
            'user' => $this->accounts->find((int) $request->user()['user_id']),
        ]);
    }

    public function updatePassword(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'current_password' => 'required|string|max:255',
            'new_password' => 'required|string|min:8|max:255',
        ]);

        $current = $request->user();
        $record = $this->users->findByLogin((string) $current['username']);
        if ($record === null || empty($record['password'])
            || !password_verify((string) $data['current_password'], (string) $record['password'])) {
            throw new BusinessRuleException('Mật khẩu hiện tại không đúng');
        }

        $this->accounts->update((int) $current['user_id'], ['password' => (string) $data['new_password']], $request);
        Session::forget('auth_token');
        Session::forget('auth_user_id');
        Flash::put('success', 'Đổi mật khẩu thành công, vui lòng đăng nhập lại');
        return $this->redirect('/admin/login');
    }
}
