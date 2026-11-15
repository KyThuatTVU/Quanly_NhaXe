<?php

declare(strict_types=1);

function test_run(array $tests): int
{
    $failed = 0;
    foreach ($tests as $name => $test) {
        try {
            $test();
            echo '  PASS  ' . $name . PHP_EOL;
        } catch (Throwable $exception) {
            $failed++;
            echo '  FAIL  ' . $name . PHP_EOL;
            echo '        ' . str_replace(PHP_EOL, PHP_EOL . '        ', $exception->getMessage()) . PHP_EOL;
        }
    }
    return $failed;
}

function assert_true(mixed $condition, string $message = ''): void
{
    if ($condition !== true) {
        throw new RuntimeException(($message !== '' ? $message . ': ' : '') . 'mong đợi true, nhận ' . var_export($condition, true));
    }
}

function assert_false(mixed $condition, string $message = ''): void
{
    if ($condition !== false) {
        throw new RuntimeException(($message !== '' ? $message . ': ' : '') . 'mong đợi false, nhận ' . var_export($condition, true));
    }
}

function assert_same(mixed $expected, mixed $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            ($message !== '' ? $message . ': ' : '')
            . 'mong đợi ' . var_export($expected, true)
            . ', nhận ' . var_export($actual, true),
        );
    }
}

function assert_contains(string $needle, string $haystack, string $message = ''): void
{
    if (!str_contains($haystack, $needle)) {
        throw new RuntimeException(
            ($message !== '' ? $message . ': ' : '') . 'không tìm thấy "' . $needle . '" trong nội dung trả về',
        );
    }
}

function assert_throws(string $class, callable $callback, string $message = ''): Throwable
{
    try {
        $callback();
    } catch (Throwable $exception) {
        if (!$exception instanceof $class) {
            throw new RuntimeException(
                ($message !== '' ? $message . ': ' : '')
                . 'mong đợi ngoại lệ ' . $class . ', nhận ' . $exception::class . ' (' . $exception->getMessage() . ')',
            );
        }
        return $exception;
    }

    throw new RuntimeException(($message !== '' ? $message . ': ' : '') . 'không có ngoại lệ ' . $class . ' được ném ra');
}
