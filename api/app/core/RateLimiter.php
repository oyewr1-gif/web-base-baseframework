<?php
class RateLimiter {
    // Default: Maksimal 60 request per 60 detik (1 menit) per IP
    public static function check($limit = 60, $windowSeconds = 60) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown_ip';
        $cacheFile = sys_get_temp_dir() . '/api_rate_' . md5($ip) . '.json';
        $currentTime = time();

        $data = [
            'count'      => 0,
            'reset_time' => $currentTime + $windowSeconds
        ];

        if (file_exists($cacheFile)) {
            $stored = json_decode(file_get_contents($cacheFile), true);
            if ($stored && $currentTime < $stored['reset_time']) {
                $data = $stored;
            }
        }

        $data['count']++;
        file_put_contents($cacheFile, json_encode($data));

        // Kirim informasi sisa kuota request di header HTTP
        $remaining = max(0, $limit - $data['count']);
        header("X-RateLimit-Limit: $limit");
        header("X-RateLimit-Remaining: $remaining");
        header("X-RateLimit-Reset: " . $data['reset_time']);

        if ($data['count'] > $limit) {
            http_response_code(429);
            echo json_encode([
                'status'      => 'fail',
                'message'     => 'Too Many Requests: Terlalu banyak permintaan, coba lagi nanti.',
                'retry_after' => $data['reset_time'] - $currentTime . ' detik'
            ]);
            exit;
        }
    }
}
