# Arsitektur Sistem

Dokumen ini menjelaskan alur kerja (*lifecycle*), struktur direktori, serta tanggung jawab setiap komponen inti di dalam **Almuna Front-End Framework**.

## Struktur Direktori

```text
almuna-frontend/
├── app/
│   ├── Core/                # Komponen inti framework
│   │   ├── ApiClient.php
│   │   ├── App.php
│   │   ├── CoreController.php
│   │   ├── CoreModule.php
│   │   ├── Env.php
│   │   ├── RateLimiter.php
│   │   └── Security.php
│   ├── Modules/             # Modul fitur aplikasi (Controller + Module)
│   │   ├── Auth/
│   │   ├── Home/            # Modul halaman utama (Default)
│   │   └── Produk/
│   ├── Views/               # Layer antarmuka pengguna (HTML/PHP)
│   │   ├── auth/
│   │   ├── home/
│   │   ├── layouts/         # Template layout utama (header & footer)
│   │   └── produk/
│   └── init.php             # Bootstrapper, Konstanta Global & Autoloader
├── public/
│   └── index.php            # Entry point publik tunggal
└── .htaccess            # URL Rewriting ke index.php (termasuk root URL)
├──.env                     # Variabel lingkungan (Environment)
```

## Siklus Hidup Permintaan (Request Lifecycle)

1. **Entry Point (`public/index.php`):**
   Semua permintaan HTTP masuk melalui `public/.htaccess` menuju `public/index.php`. File ini hanya bertugas memanggil `app/init.php` dan menginstansiasi kelas `App`.
2. **Inisialisasi (`app/init.php`):**
   * Mendefinisikan konstanta global `APP_PATH` dan `ROOT_PATH`.
   * Memulai sesi PHP dengan flag keamanan ketat (`HttpOnly` dan `SameSite=Strict`).
   * Mendaftarkan *autoloader* (`spl_autoload_register`) untuk memuat kelas dari `Core/` dan `Modules/` secara otomatis.
   * Memuat variabel `.env` melalui `Env::load()`.
   * Memeriksa batas laju permintaan melalui `RateLimiter::check()`.
3. **Routing (`App.php`):**
   Memecah parameter `$_GET['url']` dengan pola `/{module}/{method}/{param1}/{param2}...`. Jika URL diakses tanpa segmen modul (root URL), sistem secara bawaan mengarahkan permintaan ke `HomeController::index()`.
4. **Controller & Module (`CoreController` & `CoreModule`):**
   * `CoreController` memvalidasi token CSRF, menyaring input melalui `Security::cleanInput()`, memetakan payload, dan merender tampilan dengan pembungkus *layout* utama (`layouts/header.php` dan `layouts/footer.php`).
   * `CoreModule` meneruskan permintaan ke `ApiClient` sesuai pemetaan *endpoint* REST API.
5. **Komunikasi API (`ApiClient.php`):**
   Mengirim *request* JSON menggunakan `cURL`, menyematkan header `Authorization: Bearer <token>` bila diperlukan, dan menerjemahkan kode status HTTP (`200`, `201`, `400`, `401`, `404`, `429`).
