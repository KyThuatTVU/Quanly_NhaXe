<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private string $body = '',
        private int $status = 200,
        private array $headers = [],
    ) {
    }

    public static function json(mixed $data, int $status = 200): self
    {
        return new self(
            (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $status,
            ['Content-Type' => 'application/json; charset=utf-8'],
        );
    }

    public static function ok(mixed $data = null, string $message = ''): self
    {
        return self::json(['success' => true, 'message' => $message, 'data' => $data]);
    }

    public static function created(mixed $data = null, string $message = 'Tạo mới thành công'): self
    {
        return self::json(['success' => true, 'message' => $message, 'data' => $data], 201);
    }

    /**
     * @param array<string, mixed> $errors
     */
    public static function fail(string $message, int $status = 400, array $errors = []): self
    {
        return self::json(['success' => false, 'message' => $message, 'errors' => (object) $errors], $status);
    }

    public static function redirect(string $url, int $status = 302): self
    {
        return new self('', $status, ['Location' => $url]);
    }

    public static function raw(string $body, int $status = 200, array $headers = []): self
    {
        return new self($body, $status, $headers);
    }

    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }
        echo $this->body;
    }
}
