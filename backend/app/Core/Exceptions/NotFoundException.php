<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

final class NotFoundException extends HttpException
{
    public function __construct(string $message = 'Không tìm thấy dữ liệu')
    {
        parent::__construct($message, 404);
    }
}
