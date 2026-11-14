<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\Domain\Services\ContactService;

final class ContactController extends ApiController
{
    public function __construct(private readonly ContactService $contacts)
    {
    }

    public function store(Request $request): Response
    {
        return Response::created(
            $this->contacts->create($request->all(), $request),
            'Gửi liên hệ thành công, chúng tôi sẽ phản hồi sớm nhất',
        );
    }

    public function adminIndex(Request $request): Response
    {
        $filters = [
            'status' => $request->query('status'),
            'search' => $request->query('search'),
        ];
        return Response::ok($this->contacts->paginate($filters, $this->page($request), $this->perPage($request)));
    }

    public function adminShow(string $id): Response
    {
        return Response::ok($this->contacts->find($this->id($id)));
    }

    public function updateStatus(Request $request, string $id): Response
    {
        $status = (string) $request->input('status', '');
        if ($status === '') {
            throw new ValidationException(['status' => 'Trạng thái không được để trống.']);
        }
        return Response::ok(
            $this->contacts->updateStatus($this->id($id), $status, $request),
            'Cập nhật trạng thái liên hệ thành công',
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        $this->contacts->delete($this->id($id), $request);
        return Response::ok(null, 'Xóa liên hệ thành công');
    }
}
