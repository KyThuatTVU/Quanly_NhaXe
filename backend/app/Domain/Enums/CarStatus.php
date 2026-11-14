<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum CarStatus: string
{
    case Available = 'available';
    case Unavailable = 'unavailable';

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
