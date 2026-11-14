<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class LastAdminException extends BusinessRuleException
{
    public function __construct(string $message = 'Không thể khóa hoặc xóa quản trị viên cuối cùng')
    {
        parent::__construct($message);
    }
}
