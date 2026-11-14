<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum ContactStatus: string
{
    case New = 'new';
    case Processing = 'processing';
    case Replied = 'replied';

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
