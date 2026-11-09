<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Muat file .env secara otomatis
require_once 'core/DotEnv.php';
DotEnv::load(ROOT_PATH);

// Aktifkan Global Error Handler segera setelah .env dimuat
require_once 'core/ErrorHandler.php';
ErrorHandler::register();

// Jalankan fungsi autoloader otomatis
spl_autoload_register(function ($className) {
    // Tentukan direktori tempat mencari class
    $directories = [
        APP_PATH . 'core/',
        APP_PATH . 'config/',
        APP_PATH . 'helpers/',
        //APP_PATH . 'controllers/',
        //APP_PATH . 'models/'
    ];

    // Loop setiap direktori untuk mencari file class yang sesuai
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});


