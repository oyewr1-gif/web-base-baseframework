<?php
class JWT {
    private static function base64UrlEncode($text) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    private static function base64UrlDecode($text) {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $text));
    }

    // Fungsi membuat Token setelah user berhasil login
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

    // Fungsi memvalidasi Token yang dikirim di Header request klien
    public static function validateAndDecode() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            return false;
        }

        // Format header biasanya: "Bearer <token>"
        $authHeader = $headers['Authorization'];
        $tokenParts = explode(" ", $authHeader);
        if (count($tokenParts) !== 2 || strtolower($tokenParts[0]) !== 'bearer') {
            return false;
        }

        $jwt = $tokenParts[1];
        $tokenSegments = explode(".", $jwt);
        if (count($tokenSegments) !== 3) {
            return false;
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $tokenSegments;
        $secret = $_ENV['JWT_SECRET'];

        // Cek Signature
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $expectedSignature = self::base64UrlEncode($signature);

        if ($base64UrlSignature !== $expectedSignature) {
            return false; // Token dimodifikasi / tidak valid
        }

        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);

        // Cek Waktu Kadaluarsa
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false; // Token kadaluarsa
        }

        return $payload; // Kembalikan data user di dalam token
    }
}
