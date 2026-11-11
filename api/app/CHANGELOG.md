# Changelog

Semua perubahan penting pada proyek **REST API PHP MVC Native** ini akan dicatat di dalam file ini.

## - 2026-11-11
### Ditambahkan
* **Global Error Handler (`ErrorHandler.php`):** Menangkap *Exception*, *PHP Warning/Notice*, dan *Fatal Error* menjadi respon JSON standar.
* **Mode Lingkungan (`APP_ENV`):** Dukungan pemisahan output error antara mode `development` (dengan *stack trace*) dan `production` (pesan error umum demi keamanan).
* **Unit Testing:** Penambahan kerangka pengujian otomatis menggunakan PHPUnit untuk menguji komponen `Validator` dan `JWT`.

## - 2026-11-11
### Ditambahkan
* **Otentikasi JWT (`JWT.php`):** Implementasi pembuatan dan validasi token JWT secara *native* menggunakan algoritma HS256.
* **Validasi Input (`Validator.php`):** Sistem validasi *payload* otomatis dengan aturan `required`, `numeric`, dan `min`.
* Proteksi *endpoint* `DELETE /produk/hapus/{id}` menggunakan verifikasi token JWT.

## - 2026-11-11
### Ditambahkan
* **Auto-Migration Database & Tabel:** Pembuatan database dan tabel secara otomatis oleh `Model.php` saat inisialisasi pertama kali jika belum tersedia di server MySQL.

## - 2026-11-11
### Ditambahkan
* **Koneksi Database PDO:** Integrasi MySQL menggunakan PDO di dalam *Core Model*.
* **Core Model CRUD (`Model.php`):** Metode bawaan `all()`, `find()`, `create()`, `update()`, dan `delete()`.
* **Environment Loader (`DotEnv.php`):** Membaca konfigurasi kredensial dari file `.env` secara *native*.
* **Inisialisasi Terpusat (`app/init.php`):** Pemisahan pemuatan sistem *core* dari *entry point* `public/index.php`.

## - 2026-11-11
### Ditambahkan
* Rilis awal struktur dasar arsitektur **PHP MVC Native**.
* Sistem *Routing* dinamis (`App.php`) dan *URL Rewriting* (`.htaccess`).
* *Base Controller* (`Controller.php`) dengan fungsi pembantu respon JSON.
