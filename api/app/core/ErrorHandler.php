<?php
class ErrorHandler {
    
    public static function register() {
        // Tangkap Exception yang tidak tertangkap oleh try-catch biasa
        set_exception_handler([self::class, 'handleException']);
        
        // Tangkap Error standar PHP (Warning, Notice, dll) dan ubah menjadi Exception
        set_error_handler([self::class, 'handleError']);
        
        // Tangkap Fatal Error saat script mati mendadak
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleException($exception) {
        $statusCode = 500;
        
        // Anda bisa memetakan kode status HTTP berdasarkan tipe exception jika perlu
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        }

        http_response_code($statusCode);

        // Cek mode aplikasi (Development vs Production)
        // Di produksi, sembunyikan detail error agar aman
        $isDevelopment = isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development';

        $response = [
            'status'  => 'error',
            'message' => $statusCode === 500 && !$isDevelopment ? 'Internal Server Error' : $exception->getMessage()
        ];

        // Jika dalam mode development, tampilkan trace lengkap untuk debugging
        if ($isDevelopment) {
            $response['debug'] = [
                'type' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString())
            ];
        }

        echo json_encode($response);
        exit;
    }

    public static function handleError($level, $message, $file, $line) {
        // Ubah error standar menjadi ErrorException agar bisa ditangkap oleh handleException
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function handleShutdown() {
        $error = error_get_last();
        // Jika ada fatal error sebelum script selesai
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            // Bersihkan output buffer yang terlanjur tercetak agar tidak merusak JSON
            if (ob_get_length()) ob_clean(); 
            
            self::handleException(new ErrorException(
                $error['message'], 0, $error['type'], $error['file'], $error['line']
            ));
        }
    }
}
