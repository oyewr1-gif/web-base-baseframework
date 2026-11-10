<?php
//echo "index.php";
// Mengatur header agar menerima akses dari luar (CORS) dan format JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Definisikan ROOT_PATH untuk mendeteksi folder utama tempat file .env berada
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR);
// Definisikan APP_PATH ke folder app induk secara absolut
define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);
//echo ROOT_PATH . "\r\n";
//echo APP_PATH . "\r\n";
// Panggil konfigurasi autoloader
require_once APP_PATH . 'init.php';

// Jalankan aplikasi
$app = new App();

