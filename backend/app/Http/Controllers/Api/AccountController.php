<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\AccountService;

final class AccountController extends ApiController
{
    public function __construct(private readonly AccountService $accounts)
    {
    }

    public function index(Request $request): Response
    {
        $search = $request->query('search');
        return Response::ok($this->accounts->paginate(
            $search !== null ? (string) $search : null,
            $this->page($request),
            $this->perPage($request),
        ));
    }

    public function show(string $id): Response
    {
        return Response::ok($this->accounts->find($this->id($id)));
    }

    public function store(Request $request): Response
    {
        return Response::created($this->accounts->create($request->all(), $request), 'Thêm tài khoản thành công');
    }

    public function update(Request $request, string $id): Response
    {
        return Response::ok(
            $this->accounts->update($this->id($id), $request->all(), $request),
            'Cập nhật tài khoản thành công',
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        $this->accounts->delete($this->id($id), $request);
        return Response::ok(null, 'Xóa tài khoản thành công');
    }
}
