<?php

declare(strict_types=1);

namespace App\Support;

use App\Core\Exceptions\ValidationException;

/**
 * Validator tối giản: rules viết dạng "required|string|max:100".
 * Trả về mảng dữ liệu đã trim, ném ValidationException (422) nếu sai.
 */
final class Validator
{
    private const LABELS = [
        'username' => 'Tên đăng nhập',
        'password' => 'Mật khẩu',
        'current_password' => 'Mật khẩu hiện tại',
        'new_password' => 'Mật khẩu mới',
        'email' => 'Email',
        'phone' => 'Số điện thoại',
        'role' => 'Vai trò',
        'name' => 'Tên xe',
        'title' => 'Tiêu đề',
        'slug' => 'Slug',
        'seats' => 'Số chỗ',
        'seats_min' => 'Số chỗ tối thiểu',
        'seats_max' => 'Số chỗ tối đa',
        'service_name' => 'Tên dịch vụ',
        'customer_name' => 'Họ tên khách',
        'customer_phone' => 'Số điện thoại khách',
        'customer_email' => 'Email khách',
        'pickup_location' => 'Điểm đón',
        'dropoff_location' => 'Điểm đến',
        'pickup_date' => 'Ngày đón',
        'pickup_time' => 'Giờ đón',
        'return_date' => 'Ngày trả',
        'return_time' => 'Giờ trả',
        'number_of_passengers' => 'Số hành khách',
        'booking_code' => 'Mã đơn',
        'rating' => 'Số sao',
        'comment' => 'Nội dung đánh giá',
        'message' => 'Nội dung liên hệ',
        'subject' => 'Tiêu đề liên hệ',
        'vehicle_id' => 'Xe',
        'vehicle_type_id' => 'Loại xe',
        'service_id' => 'Dịch vụ',
        'status' => 'Trạng thái',
        'price_per_day' => 'Giá theo ngày',
        'price_per_km' => 'Giá theo km',
        'price' => 'Giá',
        'total_amount' => 'Tổng tiền',
        'category' => 'Danh mục',
        'content' => 'Nội dung',
        'notes' => 'Ghi chú',
        'image' => 'Ảnh',
        'image_url' => 'Đường dẫn ảnh',
        'icon' => 'Biểu tượng',
        'features' => 'Tiện ích',
        'display_order' => 'Thứ tự hiển thị',
        'is_active' => 'Trạng thái hoạt động',
        'company_name' => 'Tên công ty',
        'address' => 'Địa chỉ',
        'hotline' => 'Hotline',
        'zalo' => 'Zalo',
        'facebook' => 'Facebook',
        'working_hours' => 'Giờ làm việc',
        'map_location' => 'Bản đồ',
        'description' => 'Mô tả',
    ];

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @return array<string, mixed>
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        $clean = [];

        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $nullable = in_array('nullable', $ruleList, true);
            $value = $data[$field] ?? null;
            if (is_string($value)) {
                $value = trim($value);
            }
            $label = self::LABELS[$field] ?? $field;

            if ($nullable && ($value === null || $value === '')) {
                $clean[$field] = null;
                continue;
            }
            if ($value === '' || $value === null) {
                $clean[$field] = $value;
                if (in_array('required', $ruleList, true)) {
                    $errors[$field] = "{$label} không được để trống.";
                }
                continue;
            }

            foreach ($ruleList as $rule) {
                $error = self::check($rule, $value, $label);
                if ($error !== null) {
                    $errors[$field] = $error;
                    break;
                }
            }
            $clean[$field] = $value;
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
        return $clean;
    }

    private static function check(string $rule, mixed $value, string $label): ?string
    {
        [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);

        return match ($name) {
            'required', 'nullable' => null,
            'string' => is_string($value) ? null : "{$label} phải là chuỗi.",
            'integer' => filter_var($value, FILTER_VALIDATE_INT) !== false ? null : "{$label} phải là số nguyên.",
            'numeric' => is_numeric($value) ? null : "{$label} phải là số.",
            'boolean' => in_array($value, [true, false, 0, 1, '0', '1'], true) ? null : "{$label} phải là true/false.",
            'array' => is_array($value) ? null : "{$label} phải là mảng.",
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false ? null : "{$label} không đúng định dạng email.",
            'phone' => self::isPhone((string) $value) ? null : "{$label} không đúng định dạng (VD: 0912345678).",
            'date' => self::isDate((string) $value) ? null : "{$label} phải có định dạng YYYY-MM-DD.",
            'time' => preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', (string) $value) === 1 ? null : "{$label} phải có định dạng HH:MM.",
            'today_or_future' => self::isDate((string) $value) && $value >= date('Y-m-d') ? null : "{$label} không được ở quá khứ.",
            'min' => self::checkMin($value, (float) $param, $label),
            'max' => self::checkMax($value, (float) $param, $label),
            'in' => in_array((string) $value, explode(',', (string) $param), true) ? null : "{$label} không hợp lệ.",
            default => null,
        };
    }

    private static function checkMin(mixed $value, float $min, string $label): ?string
    {
        if (is_numeric($value)) {
            return (float) $value >= $min ? null : "{$label} phải lớn hơn hoặc bằng {$min}.";
        }
        return mb_strlen((string) $value) >= $min ? null : "{$label} phải có ít nhất {$min} ký tự.";
    }

    private static function checkMax(mixed $value, float $max, string $label): ?string
    {
        if (is_numeric($value)) {
            return (float) $value <= $max ? null : "{$label} phải nhỏ hơn hoặc bằng {$max}.";
        }
        return mb_strlen((string) $value) <= $max ? null : "{$label} không được vượt quá {$max} ký tự.";
    }

    public static function isPhone(string $value): bool
    {
        $digits = preg_replace('/[\s.\-()]/', '', $value) ?? '';
        return preg_match('/^(0|\+84)\d{8,10}$/', $digits) === 1;
    }

    public static function isDate(string $value): bool
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        return $date !== false && $date->format('Y-m-d') === $value;
    }

    public static function digits(string $value): string
    {
        return preg_replace('/[\s.\-()]/', '', $value) ?? $value;
    }
}
