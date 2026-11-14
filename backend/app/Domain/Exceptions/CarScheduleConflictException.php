<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class CarScheduleConflictException extends BusinessRuleException
{
    public function __construct(string $message = 'Xe đã có đơn trong khoảng thời gian này')
    {
        parent::__construct($message);
    }
}
