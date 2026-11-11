<?php
class Security {
    // Bersihkan input dari spasi berlebih, null-byte, dan tag berbahaya
    public static function cleanInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'cleanInput'], $data);
        }
        $data = trim($data);
        $data = stripslashes($data);
        return strip_tags($data);
    }

    // Output encoding untuk mencegah XSS pada View
    public static function e($string) {
        return htmlspecialchars((string) $string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    // Generate & validasi CSRF Token
    public static function csrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string) $token)) {
            http_response_code(403);
            die('Akses ditolak: Token CSRF tidak valid.');
        }
    }
}
