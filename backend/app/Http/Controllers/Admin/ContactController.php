<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Enums\ContactStatus;
use App\Domain\Services\ContactService;
use App\Domain\Services\SettingService;
use App\Domain\Services\UploadService;
use App\Support\Flash;
use App\Support\Paginator;

final class ContactController extends AdminController
{
    public function __construct(
        SettingService $settings,
        UploadService $uploads,
        private readonly ContactService $contacts,
    ) {
        parent::__construct($settings, $uploads);
    }

    public function index(Request $request): Response
    {
        $filters = [
            'status' => (string) $request->query('status', ''),
            'search' => (string) $request->query('search', ''),
        ];
        $contacts = $this->contacts->paginate($filters, $this->page($request), $this->perPage($request));

        return $this->adminView('admin/contacts/index', $request, [
            'title' => 'Quản lý liên hệ',
            'contacts' => $contacts,
            'filters' => $filters,
            'statuses' => ContactStatus::values(),
            'pagination' => Paginator::window($contacts, url('/admin/contacts'), array_filter($filters, static fn ($v) => $v !== '')),
        ]);
    }

    public function show(Request $request, string $id): Response
    {
        return $this->adminView('admin/contacts/show', $request, [
            'title' => 'Chi tiết liên hệ',
            'contact' => $this->contacts->find($this->intParam($id)),
            'statuses' => ContactStatus::values(),
        ]);
    }

    public function updateStatus(Request $request, string $id): Response
    {
        $contact = $this->contacts->updateStatus(
            $this->intParam($id),
            (string) $request->input('status', ''),
            $request,
        );
        Flash::put('success', 'Đã cập nhật trạng thái liên hệ #' . $contact['contact_id']);
        return $this->back($request, '/admin/contacts');
    }

    public function destroy(Request $request, string $id): Response
    {
        $id = $this->intParam($id);
        $this->contacts->delete($id, $request);
        Flash::put('success', 'Đã xóa liên hệ #' . $id);
        return $this->back($request, '/admin/contacts');
    }
}
