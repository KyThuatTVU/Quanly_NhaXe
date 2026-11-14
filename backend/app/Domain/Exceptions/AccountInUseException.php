<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class AccountInUseException extends BusinessRuleException
{
    public function __construct(string $message = 'Tài khoản đang có đơn đặt hoặc đánh giá, không thể xóa')
    {
        parent::__construct($message);
    }
}
