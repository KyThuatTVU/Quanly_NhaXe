<?php

declare(strict_types=1);

namespace App\Support;

final class Str
{
    private const MAP = [
        'a' => 'áàảãạăắằẳẵặâấầẩẫậäåā',
        'A' => 'ÁÀẢÃẠĂẮẰẲẴẶÂẤẦẨẪẬÄÅĀ',
        'e' => 'éèẻẽẹêếềểễệëē',
        'E' => 'ÉÈẺẼẸÊẾỀỂỄỆËĒ',
        'i' => 'íìỉĩịîïī',
        'I' => 'ÍÌỈĨỊÎÏĪ',
        'o' => 'óòỏõọôốồổỗộơớờởỡợöō',
        'O' => 'ÓÒỎÕỌÔỐỒỔỖỘƠỚỜỞỠỢÖŌ',
        'u' => 'úùủũụưứừửữựûüū',
        'U' => 'ÚÙỦŨỤƯỨỪỬỮỰÛÜŪ',
        'y' => 'ýỳỷỹỵÿ',
        'Y' => 'ÝỲỶỸỴŸ',
        'd' => 'đ',
        'D' => 'Đ',
    ];

    public static function slug(string $text): string
    {
        static $table = null;
        if ($table === null) {
            $table = [];
            foreach (self::MAP as $ascii => $chars) {
                foreach (mb_str_split($chars) as $char) {
                    $table[$char] = $ascii;
                }
            }
        }

        $text = strtr($text, $table);
        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-');
    }

    public static function bookingCode(string $prefix = 'TV'): string
    {
        return $prefix . date('ymd') . strtoupper(bin2hex(random_bytes(2)));
    }

    public static function token(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }
}
