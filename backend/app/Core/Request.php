<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    /** @var array<string, mixed> */
    private array $routeParams = [];

    /** @var array<string, mixed> */
    private array $attributes = [];

    private ?array $jsonCache = null;

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $post
     * @param array<string, mixed> $files
     * @param array<string, string> $headers
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $post,
        private readonly array $files,
        private readonly array $headers,
        private readonly string $rawBody,
        private readonly string $ip,
    ) {
    }

    public static function capture(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper((string) $_POST['_method']);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        if ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }
        $path = '/' . trim($uri, '/');

        $headers = [];
        foreach (function_exists('getallheaders') ? (getallheaders() ?: []) : [] as $key => $value) {
            $headers[strtolower((string) $key)] = (string) $value;
        }

        return new self(
            $method,
            $path,
            $_GET,
            $_POST,
            $_FILES,
            $headers,
            (string) file_get_contents('php://input'),
            (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'),
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $body = $this->json();
        if (array_key_exists($key, $body)) {
            return $body[$key];
        }
        if (array_key_exists($key, $this->post)) {
            return $this->post[$key];
        }
        return $this->query[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return array_merge($this->query, $this->post, $this->json());
    }

    /**
     * @return array<string, mixed>
     */
    public function json(): array
    {
        if ($this->jsonCache !== null) {
            return $this->jsonCache;
        }
        $contentType = $this->header('content-type', '');
        if ($this->rawBody === '' || !str_contains($contentType, 'json')) {
            return $this->jsonCache = [];
        }
        $decoded = json_decode($this->rawBody, true);
        return $this->jsonCache = is_array($decoded) ? $decoded : [];
    }

    public function header(string $key, ?string $default = null): ?string
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    public function ip(): string
    {
        return $this->ip;
    }

    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }

    /**
     * @param array<string, mixed> $file
     */
    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;
        return is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE ? $file : null;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function routeParam(string $key, mixed $default = null): mixed
    {
        return $this->routeParams[$key] ?? $default;
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function attribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function user(): ?array
    {
        $user = $this->attribute('auth');
        return is_array($user) ? $user : null;
    }

    public function userId(): ?int
    {
        $user = $this->user();
        return isset($user['user_id']) ? (int) $user['user_id'] : null;
    }
}
