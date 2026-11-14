<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exceptions\NotFoundException;
use App\Http\Middleware\MiddlewareInterface;

final class Router
{
    /** @var array<int, array{method: string, regex: string, handler: array|callable, middleware: array}> */
    private array $routes = [];

    private string $prefix = '';

    /** @var array<int, mixed> */
    private array $groupMiddleware = [];

    /**
     * @param array<int, mixed> $middleware
     */
    public function group(string $prefix, array $middleware, callable $callback): void
    {
        $previousPrefix = $this->prefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->prefix = rtrim($previousPrefix . '/' . trim($prefix, '/'), '/');
        $this->groupMiddleware = array_merge($previousMiddleware, $middleware);
        $callback($this);
        $this->prefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    /**
     * @param array|callable $handler
     * @param array<int, mixed> $middleware
     */
    public function add(string $method, string $path, array|callable $handler, array $middleware = []): self
    {
        $fullPath = $this->prefix . '/' . trim($path, '/');
        $this->routes[] = [
            'method' => strtoupper($method),
            'regex' => $this->compile($fullPath),
            'handler' => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
        return $this;
    }

    public function get(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->add('POST', $path, $handler, $middleware);
    }

    public function put(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->add('PUT', $path, $handler, $middleware);
    }

    public function patch(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->add('PATCH', $path, $handler, $middleware);
    }

    public function delete(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->add('DELETE', $path, $handler, $middleware);
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            $methodMatches = $route['method'] === $request->method()
                || ($route['method'] === 'GET' && $request->method() === 'HEAD');
            if (!$methodMatches || !preg_match($route['regex'], $request->path(), $matches)) {
                continue;
            }
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $request->setRouteParams($params);
            $pipeline = $this->buildPipeline($route, $params);
            return $pipeline($request);
        }

        throw new NotFoundException('Không tìm thấy endpoint ' . $request->method() . ' ' . $request->path());
    }

    private function compile(string $path): string
    {
        $path = '/' . trim($path, '/');
        if ($path === '/') {
            return '#^/?$#';
        }
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . rtrim((string) $pattern, '/') . '/?$#';
    }

    /**
     * @param array{handler: array|callable, middleware: array} $route
     * @param array<string, string> $params
     */
    private function buildPipeline(array $route, array $params): callable
    {
        $core = function (Request $request) use ($route, $params): Response {
            $result = Container::instance()->call(
                $route['handler'],
                array_merge($params, ['__request' => $request]),
            );
            return $result instanceof Response ? $result : Response::ok($result);
        };

        foreach (array_reverse($route['middleware']) as $middleware) {
            $next = $core;
            $core = function (Request $request) use ($middleware, $next): Response {
                $instance = $this->makeMiddleware($middleware);
                return $instance->handle($request, $next);
            };
        }

        return $core;
    }

    private function makeMiddleware(mixed $middleware): MiddlewareInterface
    {
        $params = [];
        if (is_array($middleware)) {
            [$class, $params] = [$middleware[0], (array) ($middleware[1] ?? [])];
        } else {
            $class = $middleware;
        }

        /** @var MiddlewareInterface $instance */
        $instance = Container::instance()->get($class);
        if ($params !== [] && method_exists($instance, 'setParams')) {
            $instance->setParams($params);
        }
        return $instance;
    }
}
