<?php
class ApiClient {
    public static function request($method, $endpoint, $data = [], $requireAuth = false) {
        $url = rtrim(Env::get('API_BASE_URL'), '/') . $endpoint;
        $ch = curl_init($url);

        $headers = ['Accept: application/json'];

        if (in_array(strtoupper($method), ['POST', 'PUT'])) {
            $headers[] = 'Content-Type: application/json';
        }

        if ($requireAuth) {
            if (empty($_SESSION['jwt_token'])) {
                return ['status_code' => 401, 'body' => ['message' => 'Token JWT tidak ditemukan']];
            }
            $headers[] = 'Authorization: Bearer ' . $_SESSION['jwt_token'];
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 15
        ]);

        if (!empty($data) && in_array(strtoupper($method), ['POST', 'PUT'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Hapus sesi jika server API merespons 401 Unauthorized
        if ($httpCode === 401 && $requireAuth) {
            unset($_SESSION['jwt_token'], $_SESSION['user']);
        }

        return [
            'status_code' => $httpCode,
            'body'        => json_decode($response, true) ?? []
        ];
    }
}
