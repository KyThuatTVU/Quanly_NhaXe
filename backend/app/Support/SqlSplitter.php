<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Tách file .sql thành từng câu lệnh, bỏ qua dấu ; trong chuỗi và trong comment.
 */
final class SqlSplitter
{
    /**
     * @return array<int, string>
     */
    public static function split(string $sql): array
    {
        $statements = [];
        $current = '';
        $inSingle = $inDouble = $inBacktick = false;
        $inLineComment = $inBlockComment = false;
        $length = strlen($sql);

        for ($index = 0; $index < $length; $index++) {
            $char = $sql[$index];
            $next = $index + 1 < $length ? $sql[$index + 1] : '';

            if ($inLineComment) {
                if ($char === "\n") {
                    $inLineComment = false;
                    $current .= $char;
                }
                continue;
            }
            if ($inBlockComment) {
                if ($char === '*' && $next === '/') {
                    $inBlockComment = false;
                    $index++;
                }
                continue;
            }
            if ($inSingle) {
                $current .= $char;
                if ($char === '\\' && $next !== '') {
                    $current .= $next;
                    $index++;
                } elseif ($char === "'") {
                    $inSingle = false;
                }
                continue;
            }
            if ($inDouble) {
                $current .= $char;
                if ($char === '\\' && $next !== '') {
                    $current .= $next;
                    $index++;
                } elseif ($char === '"') {
                    $inDouble = false;
                }
                continue;
            }
            if ($inBacktick) {
                $current .= $char;
                if ($char === '`') {
                    $inBacktick = false;
                }
                continue;
            }

            if ($char === '-' && $next === '-') {
                $inLineComment = true;
                $index++;
                continue;
            }
            if ($char === '#') {
                $inLineComment = true;
                continue;
            }
            if ($char === '/' && $next === '*') {
                $inBlockComment = true;
                $index++;
                continue;
            }
            if ($char === "'") {
                $inSingle = true;
                $current .= $char;
                continue;
            }
            if ($char === '"') {
                $inDouble = true;
                $current .= $char;
                continue;
            }
            if ($char === '`') {
                $inBacktick = true;
                $current .= $char;
                continue;
            }
            if ($char === ';') {
                $statements[] = trim($current);
                $current = '';
                continue;
            }
            $current .= $char;
        }

        if (trim($current) !== '') {
            $statements[] = trim($current);
        }

        return array_values(array_filter($statements, static fn (string $statement): bool => $statement !== ''));
    }
}
