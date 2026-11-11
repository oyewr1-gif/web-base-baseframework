<?php
class JWT {
    private static function base64UrlEncode($text) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    private static function base64UrlDecode($text) {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $text));
    }

    public static function generate($payload) {
        $secret = $_ENV['JWT_SECRET'];
        $expiry = time() + (int)$_ENV['JWT_EXPIRY'];

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['exp'] = $expiry;
        $payloadJson = json_encode($payload);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payloadJson);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function validateAndDecode() {
        $headers = getallheaders();
        // Mendukung huruf besar/kecil pada key Authorization di berbagai web server
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if (!$authHeader) {
            return false;
        }

        $tokenParts = explode(" ", $authHeader);
        if (count($tokenParts) !== 2 || strtolower($tokenParts) !== 'bearer') {
            return false;
        }

        $jwt = $tokenParts;
        $tokenSegments = explode(".", $jwt);
        if (count($tokenSegments) !== 3) {
            return false;
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $tokenSegments;
        $secret = $_ENV['JWT_SECRET'];

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $expectedSignature = self::base64UrlEncode($signature);

        // PERBAIKAN: Gunakan hash_equals untuk mencegah Timing Attack
        if (!hash_equals($expectedSignature, $base64UrlSignature)) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }
}
