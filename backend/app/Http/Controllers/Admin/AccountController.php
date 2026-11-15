<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Enums\Role;
use App\Domain\Services\AccountService;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Support\Flash;
use App\Support\Paginator;

final class AccountController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly AccountService $accounts,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $users = $this->accounts->paginate(
            $search !== '' ? $search : null,
            $this->page($request),
            $this->perPage($request),
        );

        return $this->adminView('admin/accounts/index', $request, [
            'title' => 'Tài khoản',
            'users' => $users,
            'search' => $search,
            'pagination' => Paginator::window($users, url('/admin/accounts'), $search !== '' ? ['search' => $search] : []),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->adminView('admin/accounts/form', $request, [
            'title' => 'Thêm tài khoản',
            'user' => null,
            'roles' => Role::values(),
        ]);
    }

    public function store(Request $request): Response
    {
        $user = $this->accounts->create($request->all(), $request);
        Flash::put('success', 'Đã thêm tài khoản ' . $user['username']);
        return $this->redirect('/admin/accounts');
    }

    public function edit(Request $request, string $id): Response
    {
        return $this->adminView('admin/accounts/form', $request, [
            'title' => 'Sửa tài khoản',
            'user' => $this->accounts->find($this->intParam($id)),
            'roles' => Role::values(),
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $id = $this->intParam($id);
        $data = $request->all();
        if (($data['password'] ?? '') === '') {
            unset($data['password']);
        }
        $user = $this->accounts->update($id, $data, $request);
        Flash::put('success', 'Đã cập nhật tài khoản ' . $user['username']);
        return $this->redirect('/admin/accounts');
    }

    public function destroy(Request $request, string $id): Response
    {
        $id = $this->intParam($id);
        $this->accounts->delete($id, $request);
        Flash::put('success', 'Đã xóa tài khoản #' . $id);
        return $this->redirect('/admin/accounts');
    }
}
