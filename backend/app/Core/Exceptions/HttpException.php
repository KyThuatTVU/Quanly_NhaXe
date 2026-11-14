<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use RuntimeException;

class HttpException extends RuntimeException
{
    /**
     * @param array<string, mixed> $errors
     */
    public function __construct(
        string $message,
        private readonly int $statusCode = 500,
        private readonly array $errors = [],
    ) {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
