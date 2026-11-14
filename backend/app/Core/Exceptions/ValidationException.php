<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

final class ValidationException extends HttpException
{
    /**
     * @param array<string, string> $errors
     */
    public function __construct(array $errors, string $message = 'Dữ liệu không hợp lệ')
    {
        parent::__construct($message, 422, $errors);
    }
}
