<?php

declare(strict_types=1);

use App\Support\Format;
use App\Support\SqlSplitter;
use App\Support\Str;
use App\Support\Validator;

return [
    'Số điện thoại Việt Nam hợp lệ được chấp nhận' => static function (): void {
        assert_true(Validator::isPhone('0912345678'));
        assert_true(Validator::isPhone('0912 345 678'));
        assert_true(Validator::isPhone('+84912345678'));
    },

    'Số điện thoại sai bị từ chối' => static function (): void {
        assert_false(Validator::isPhone('12345'));
        assert_false(Validator::isPhone('0912abc678'));
        assert_false(Validator::isPhone(''));
    },

    'Ngày phải đúng định dạng YYYY-MM-DD' => static function (): void {
        assert_true(Validator::isDate('2026-11-15'));
        assert_false(Validator::isDate('15/11/2026'));
        assert_false(Validator::isDate('2026-02-30'));
    },

    'digits bỏ khoảng trắng và dấu phân cách' => static function (): void {
        assert_same('0912345678', Validator::digits('0912 345.678'));
    },

    'Mã đơn có tiền tố DX, ngày và 4 ký tự hex' => static function (): void {
        assert_true(preg_match('/^DX\d{6}[0-9A-F]{4}$/', Str::bookingCode()) === 1);
    },

    'Slug bỏ dấu tiếng Việt' => static function (): void {
        assert_same('xe-hop-dong-45-cho', Str::slug('Xe hợp đồng 45 chỗ'));
    },

    'money định dạng tiền Việt' => static function (): void {
        assert_same('1.500.000 ₫', Format::money(1500000));
        assert_same('—', Format::money(null));
    },

    'SqlSplitter tách câu lệnh, bỏ qua dấu ; trong chuỗi và comment' => static function (): void {
        $sql = "-- chú thích ; không tính\n"
            . "CREATE TABLE a (name VARCHAR(10) DEFAULT 'x;y');\n"
            . "/* block ; */\n"
            . 'INSERT INTO a VALUES ("b;c");';
        $statements = SqlSplitter::split($sql);
        assert_same(2, count($statements));
        assert_contains("'x;y'", $statements[0]);
        assert_contains('"b;c"', $statements[1]);
    },
];
