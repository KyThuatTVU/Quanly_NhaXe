<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Core\Config;
use App\Domain\Exceptions\BusinessRuleException;

/**
 * Luồng OAuth 2.0 "Đăng nhập bằng Google" cho trang quản trị.
 * Chỉ chạy khi .env có GOOGLE_CLIENT_ID và GOOGLE_CLIENT_SECRET.
 */
final class GoogleOAuthService
{
    private const AUTH_ENDPOINT = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';
    private const USERINFO_ENDPOINT = 'https://www.googleapis.com/oauth2/v3/userinfo';

    public function isConfigured(): bool
    {
        return $this->clientId() !== '' && $this->clientSecret() !== '';
    }

    public function authorizationUrl(string $state, string $redirectUri): string
    {
        return self::AUTH_ENDPOINT . '?' . http_build_query([
            'client_id' => $this->clientId(),
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]);
    }

    /**
     * @return array{email: string, name: string}
     */
    public function fetchProfile(string $code, string $redirectUri): array
    {
        $token = $this->request(self::TOKEN_ENDPOINT, [
            'code' => $code,
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ], []);

        $accessToken = (string) ($token['access_token'] ?? '');
        if ($accessToken === '') {
            throw new BusinessRuleException('Google không trả về mã truy cập, vui lòng thử lại');
        }

        $profile = $this->request(self::USERINFO_ENDPOINT, null, ['Authorization: Bearer ' . $accessToken]);
        $email = strtolower(trim((string) ($profile['email'] ?? '')));
        $verified = filter_var($profile['email_verified'] ?? true, FILTER_VALIDATE_BOOL);

        if ($email === '' || $verified !== true) {
            throw new BusinessRuleException('Tài khoản Google chưa xác thực email nên không thể đăng nhập');
        }

        return [
            'email' => $email,
            'name' => (string) ($profile['name'] ?? $email),
        ];
    }

    /**
     * @param array<string, string>|null $form
     * @param array<int, string> $headers
     * @return array<string, mixed>
     */
    private function request(string $url, ?array $form, array $headers): array
    {
        if (!function_exists('curl_init')) {
            throw new BusinessRuleException('Máy chủ chưa bật tiện ích cURL nên không thể đăng nhập bằng Google');
        }

        $handle = curl_init($url);
        if ($handle === false) {
            throw new BusinessRuleException('Không khởi tạo được kết nối tới Google, vui lòng thử lại');
        }

        $headers[] = 'Accept: application/json';
        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        if ($form !== null) {
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($form));
        }

        $body = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        if ($body === false || $status >= 400) {
            throw new BusinessRuleException($error !== ''
                ? 'Không kết nối được tới Google: ' . $error
                : 'Google từ chối yêu cầu đăng nhập (mã ' . $status . '), vui lòng thử lại');
        }

        $data = json_decode((string) $body, true);
        if (!is_array($data)) {
            throw new BusinessRuleException('Google trả về dữ liệu không hợp lệ, vui lòng thử lại');
        }

        return $data;
    }

    private function clientId(): string
    {
        return trim((string) Config::get('auth.google.client_id', ''));
    }

    private function clientSecret(): string
    {
        return trim((string) Config::get('auth.google.client_secret', ''));
    }
}
