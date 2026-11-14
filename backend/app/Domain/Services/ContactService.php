<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Exceptions\NotFoundException;
use App\Core\Request;
use App\Domain\Enums\ContactStatus;
use App\Domain\Exceptions\BusinessRuleException;
use App\Infrastructure\Logging\AuditLogger;
use App\Infrastructure\Persistence\Repositories\PdoContactRepository;
use App\Support\Validator;

final class ContactService
{
    public function __construct(
        private readonly PdoContactRepository $contacts,
        private readonly AuditLogger $audit,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function create(array $input, Request $request): array
    {
        $data = Validator::validate($input, [
            'name' => 'required|string|max:100',
            'phone' => 'required|phone',
            'email' => 'nullable|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $id = $this->contacts->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => Validator::digits((string) $data['phone']),
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => ContactStatus::New->value,
        ]);

        $this->audit->log($request, 'contact_create', 'contacts', $id, 'Khách gửi liên hệ: ' . $data['name']);
        return $this->find($id);
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters, int $page, int $perPage): array
    {
        if (!empty($filters['status']) && !in_array($filters['status'], ContactStatus::values(), true)) {
            throw new BusinessRuleException('Trạng thái liên hệ không hợp lệ');
        }
        return $this->contacts->paginate($filters, $page, $perPage);
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $id): array
    {
        $contact = $this->contacts->find($id);
        if ($contact === null) {
            throw new NotFoundException('Không tìm thấy liên hệ');
        }
        return $contact;
    }

    /**
     * @return array<string, mixed>
     */
    public function updateStatus(int $id, string $status, Request $request): array
    {
        if (!in_array($status, ContactStatus::values(), true)) {
            throw new BusinessRuleException('Trạng thái liên hệ không hợp lệ');
        }
        $this->find($id);
        $this->contacts->updateStatus($id, $status, $request->userId());
        $this->audit->log($request, 'contact_status', 'contacts', $id, 'Đổi trạng thái liên hệ #' . $id . ' thành ' . $status);
        return $this->find($id);
    }

    public function delete(int $id, Request $request): void
    {
        $this->find($id);
        $this->contacts->delete($id);
        $this->audit->log($request, 'contact_delete', 'contacts', $id, 'Xóa liên hệ #' . $id);
    }
}
