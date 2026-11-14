<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Business = 'business';
    case Customer = 'customer';

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
