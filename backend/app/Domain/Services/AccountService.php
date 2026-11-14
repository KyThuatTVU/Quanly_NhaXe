<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Config;
use App\Core\Exceptions\NotFoundException;
use App\Core\Request;
use App\Domain\Enums\Role;
use App\Domain\Exceptions\AccountInUseException;
use App\Domain\Exceptions\BusinessRuleException;
use App\Domain\Exceptions\LastAdminException;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Repositories\PdoSessionRepository;
use App\Infrastructure\Persistence\Repositories\PdoUserRepository;
use App\Support\Validator;

final class AccountService
{
    public function __construct(
        private readonly PdoUserRepository $users,
        private readonly PdoSessionRepository $sessions,
        private readonly AuditLogger $audit,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function paginate(?string $search, int $page, int $perPage): array
    {
        return $this->users->all($search, $page, $perPage);
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $id): array
    {
        $user = $this->users->find($id);
        if ($user === null) {
            throw new NotFoundException('Không tìm thấy tài khoản');
        }
        return $user;
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function create(array $input, Request $request): array
    {
        $data = Validator::validate($input, [
            'username' => 'required|string|min:3|max:50',
            'password' => 'required|string|min:8|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|phone',
            'role' => 'required|in:admin,business,customer',
            'is_active' => 'nullable|boolean',
        ]);

        $username = (string) $data['username'];
        if (preg_match('/^[A-Za-z0-9._-]+$/', $username) !== 1) {
            throw new BusinessRuleException('Tên đăng nhập chỉ gồm chữ, số, dấu chấm, gạch dưới, gạch ngang');
        }
        if ($this->users->existsUsername($username)) {
            throw new BusinessRuleException('Tên đăng nhập đã tồn tại');
        }
        if ($this->users->existsEmail($data['email'])) {
            throw new BusinessRuleException('Email đã được sử dụng');
        }

        $id = $this->users->create([
            'username' => $username,
            'password' => password_hash((string) $data['password'], PASSWORD_DEFAULT),
            'email' => $data['email'],
            'phone' => $data['phone'] !== null ? Validator::digits((string) $data['phone']) : null,
            'role' => $data['role'],
            'is_active' => isset($input['is_active'])
                ? (filter_var($input['is_active'], FILTER_VALIDATE_BOOL) ? 1 : 0)
                : 1,
        ]);

        $this->audit->log($request, 'user_create', 'users', $id, 'Thêm tài khoản ' . $username);
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
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|phone',
            'role' => 'nullable|in:admin,business,customer',
            'password' => 'nullable|string|min:8|max:255',
            'is_active' => 'nullable|boolean',
        ];
        $data = Validator::validate(array_intersect_key($input, $rules), $rules);
        $data = array_intersect_key($data, $input);

        if (array_key_exists('email', $data) && $this->users->existsEmail($data['email'], $id)) {
            throw new BusinessRuleException('Email đã được sử dụng');
        }

        $payload = [];
        if (array_key_exists('email', $data)) {
            $payload['email'] = $data['email'];
        }
        if (array_key_exists('phone', $data)) {
            $payload['phone'] = $data['phone'] !== null ? Validator::digits((string) $data['phone']) : null;
        }

        $newRole = $current['role'];
        if (array_key_exists('role', $data) && $data['role'] !== null) {
            $newRole = (string) $data['role'];
            $payload['role'] = $newRole;
        }
        $newActive = (int) $current['is_active'];
        if (array_key_exists('is_active', $input)) {
            $newActive = filter_var($input['is_active'], FILTER_VALIDATE_BOOL) ? 1 : 0;
            $payload['is_active'] = $newActive;
        }
        if (array_key_exists('password', $data) && $data['password'] !== null && $data['password'] !== '') {
            $payload['password'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        }

        $wasAdmin = $current['role'] === Role::Admin->value && (int) $current['is_active'] === 1;
        $stillAdmin = $newRole === Role::Admin->value && $newActive === 1;
        if ($wasAdmin && !$stillAdmin && $this->users->countActiveAdmins($id) === 0) {
            throw new LastAdminException();
        }
        if ($newActive === 0 && $request->userId() === $id) {
            throw new BusinessRuleException('Bạn không thể tự khóa tài khoản đang đăng nhập');
        }

        $this->users->update($id, $payload);
        if ($newActive === 0 || (array_key_exists('password', $payload))) {
            $this->sessions->deactivateAllForUser($id);
        }
        $this->audit->log($request, 'user_update', 'users', $id, 'Cập nhật tài khoản ' . $current['username']);

        return $this->find($id);
    }

    public function delete(int $id, Request $request): void
    {
        $user = $this->find($id);
        if ($request->userId() === $id) {
            throw new BusinessRuleException('Bạn không thể xóa tài khoản đang đăng nhập');
        }
        if ($user['role'] === Role::Admin->value && (int) $user['is_active'] === 1
            && $this->users->countActiveAdmins($id) === 0) {
            throw new LastAdminException();
        }
        if ($this->users->hasBusinessData($id)) {
            throw new AccountInUseException();
        }

        $this->sessions->deactivateAllForUser($id);
        $this->users->delete($id);
        $this->audit->log($request, 'user_delete', 'users', $id, 'Xóa tài khoản ' . $user['username']);
    }

    public function passwordMinLength(): int
    {
        return (int) Config::get('auth.password_min_length', 8);
    }
}
