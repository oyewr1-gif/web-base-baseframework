#  Front-End Framework (PHP MVC)

Framework Front-End ringan berbasis arsitektur **PHP MVC (Modular)** yang dirancang khusus untuk mengonsumsi REST API dengan otentikasi **JSON Web Token (JWT)**.

## Fitur Utama

* **Modular MVC Architecture:** Pemisahan logika inti (`Core`), fitur bisnis (`Modules`), dan tampilan (`Views`) dengan modul `Home` sebagai halaman utama bawaan.
* **Unified Layout System:** Rendering antarmuka terpusat melalui `layouts/header.php` dan `layouts/footer.php` di `CoreController`.
* **Centralized CRUD:** Kelas induk `CoreController` dan `CoreModule` untuk mempercepat pembuatan modul baru tanpa duplikasi kode.
* **JWT Session Management:** Penyimpanan token JWT di sisi server (`$_SESSION` dengan `HttpOnly` & `SameSite=Strict`) agar aman dari pencurian token via XSS.
* **Multi-Layer Security:** Dilengkapi sanitasi input (`cleanInput`), output escaping (`Security::e`), proteksi CSRF, dan *Rate Limiter* terintegrasi.
* **Zero External Dependencies:** Dibangun menggunakan PHP Native (`cURL`, `spl_autoload_register`) tanpa bergantung pada *library* pihak ketiga.

## Persyaratan Sistem

* PHP >= 8.0
* Ekstensi PHP `curl` dan `json` aktif
* Web Server Apache (dengan `mod_rewrite` aktif) atau Nginx

## Instalasi & Konfigurasi

1. **Clone atau salin proyek** ke direktori web server Anda (misalnya `htdocs/baseframework`).
2. **Konfigurasi Environment:**
   Salin atau buat file `.env` di direktori utama (`ROOT_PATH/.env`) dan sesuaikan nilainya:
   ```env
   APP_NAME=" Frontend"
   APP_URL="http://localhost/baseframework/public"
   API_BASE_URL="http://localhost/baseframework/api"
   RATE_LIMIT_MAX=60
   RATE_LIMIT_WINDOW=60
   ```
3. **Arahkan Document Root:**
   Pastikan akses web diarahkan ke folder `public/` (contoh: `http://localhost/baseframework/public`).

## Cara Menambah Modul Baru

Untuk menambahkan modul baru (misalnya modul `Kategori` yang terhubung ke `/kategori` pada API):

1. Buat folder `app/Modules/Kategori/`.
2. Buat file `KategoriModule.php`:
   ```php
   <?php
   class KategoriModule extends CoreModule {
       protected \$endpoint = '/kategori';
   }
   ```
3. Buat file `KategoriController.php`:
   ```php
   <?php
   class KategoriController extends CoreController {
       protected \$viewPath = 'kategori';
       protected \$redirectPath = '/kategori';

       public function __construct() {
           \$this->module = new KategoriModule();
       }

       protected function mapPayload(array \$cleanPost) {
           return ['nama_kategori' => \$cleanPost['nama_kategori'] ?? ''];
       }
   }
   ```
4. Buat folder tampilan `app/Views/kategori/` berisi `index.php` dan `form.php` (tidak perlu menyertakan tag `<html>` atau `<head>` karena sudah otomatis dimuat oleh `layouts/header.php` dan `layouts/footer.php`).
