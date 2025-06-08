<?php
// Simple JWT encode/decode for API authentication
// Lưu ý: Để bảo mật thực tế nên dùng thư viện bên ngoài (firebase/php-jwt), ở đây là bản tối giản minh họa

define('JWT_SECRET', 'your_super_secret_key');

date_default_timezone_set('UTC');

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function jwt_encode($payload, $exp = 3600) {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $payload['exp'] = time() + $exp;
    $segments = [
        base64url_encode(json_encode($header)),
        base64url_encode(json_encode($payload))
    ];
    $signing_input = implode('.', $segments);
    $signature = hash_hmac('sha256', $signing_input, JWT_SECRET, true);
    $segments[] = base64url_encode($signature);
    return implode('.', $segments);
}

function jwt_decode($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;
    list($header64, $payload64, $sig64) = $parts;
    $header = json_decode(base64url_decode($header64), true);
    $payload = json_decode(base64url_decode($payload64), true);
    $signature = base64url_decode($sig64);
    $valid = hash_hmac('sha256', "$header64.$payload64", JWT_SECRET, true);
    if (!hash_equals($valid, $signature)) return false;
    if (isset($payload['exp']) && $payload['exp'] < time()) return false;
    return $payload;
}