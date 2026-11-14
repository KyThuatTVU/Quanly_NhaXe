<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

/**
 * DI container tối giản: autowire constructor, giữ singleton, gọi controller action.
 */
final class Container
{
    private static ?self $instance = null;

    /** @var array<string, callable|string> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public function bind(string $id, callable|string $concrete): void
    {
        $this->bindings[$id] = $concrete;
    }

    public function singleton(string $id, callable|string $concrete): void
    {
        $this->bind($id, $concrete);
        $this->instances[$id] = $concrete instanceof \Closure ? $concrete($this) : $this->build($concrete);
    }

    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        $concrete = $this->bindings[$id] ?? $id;
        if (is_string($concrete)) {
            if (!class_exists($concrete)) {
                throw new RuntimeException("Không tìm thấy lớp {$id}");
            }
            $object = $this->build($concrete);
        } else {
            $object = $concrete($this);
        }
        $this->instances[$id] = $object;
        return $object;
    }

    private function build(string $class): object
    {
        $reflection = new ReflectionClass($class);
        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Lớp {$class} không thể khởi tạo");
        }
        $constructor = $reflection->getConstructor();
        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return new $class();
        }
        return $reflection->newInstanceArgs($this->resolveParameters($constructor, []));
    }

    /**
     * @param array<string, mixed> $params
     */
    public function call(array|callable $handler, array $params = []): mixed
    {
        if (is_array($handler)) {
            $target = is_object($handler[0]) ? $handler[0] : $this->get($handler[0]);
            $reflection = new ReflectionMethod($target, $handler[1]);
            $callable = [$target, $handler[1]];
        } else {
            $reflection = new \ReflectionFunction($handler);
            $callable = $handler;
        }
        return $callable(...$this->resolveParameters($reflection, $params));
    }

    /**
     * @return array<int, mixed>
     */
    private function resolveParameters(ReflectionMethod|\ReflectionFunction $reflection, array $params): array
    {
        $args = [];
        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $className = $type->getName();
                if ($className === Request::class) {
                    $args[] = $params['__request'];
                    continue;
                }
                $args[] = $this->get($className);
                continue;
            }
            if (array_key_exists($name, $params) && $name !== '__request') {
                $args[] = $params[$name];
                continue;
            }
            if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
                continue;
            }
            throw new RuntimeException("Thiếu tham số \${$name} khi gọi {$reflection->getName()}");
        }
        return $args;
    }
}
