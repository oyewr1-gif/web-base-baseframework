<?php
// 1. Definisi Global Path Constants
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR   . DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);

// 2. Pengaturan Session yang Aman
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]);
}
//echo "init.php";
// 3. Muat Environment Variables dari ROOT_PATH
require_once APP_PATH . 'Core/Env.php';
Env::load(ROOT_PATH . '.env');

// 4. Autoloader Otomatis (Core & Modules)
spl_autoload_register(function ($className) {
    // Cek di folder app/Core/
    $coreFile = APP_PATH . 'Core/' . $className . '.php';
    if (file_exists($coreFile)) {
        require_once $coreFile;
        return;
    }

    // Cek di folder app/Modules/{NamaModul}/{NamaClass}.php
    // Contoh: ProdukController atau ProdukModule -> mencari di app/Modules/Produk/
    $moduleName = str_replace(['Controller', 'Module'], '', $className);
    $moduleFile = APP_PATH . 'Modules/' . $moduleName . '/' . $className . '.php';
    if (file_exists($moduleFile)) {
        require_once $moduleFile;
        return;
    }
});

// 5. Jalankan Rate Limiter Front-End
RateLimiter::check(
    (int) Env::get('RATE_LIMIT_MAX', 60),
    (int) Env::get('RATE_LIMIT_WINDOW', 60)
);
