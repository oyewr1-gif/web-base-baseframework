# Changelog

Seluruh perubahan penting pada proyek **Almuna Front-End Framework** dicatat dalam dokumen ini.

## - 2026-11-12
### Ditambahkan
* Konstanta global `APP_PATH` dan `ROOT_PATH` pada `app/init.php`.
* Sistem *autoloading* otomatis (`spl_autoload_register`) untuk kelas di folder `app/Core/` dan `app/Modules/`.
* Validasi keberadaan file *view* pada `CoreController::view()` menggunakan `APP_PATH`.

### Diubah
* Menghapus pemanggilan `require_once` manual pada `App.php`, `ProdukController.php`, dan `AuthController.php`.

## - 2026-11-11
### Ditambahkan
* Inisialisasi proyek terpusat di `app/init.php` dengan `public/index.php` sebagai *entry point* bersih.
* Dukungan konfigurasi lingkungan melalui file `.env` dan parser `Env.php`.
* Abstraksi CRUD terpusat melalui `CoreController` dan `CoreModule` yang selaras dengan spesifikasi REST API (`/produk`, `/produk/detail/{id}`, `/produk/store`, `/produk/ubah/{id}`, `/produk/hapus/{id}`).
* Komponen `ApiClient` berbasis `cURL` dengan dukungan otentikasi `Bearer Token (JWT)`.
* Modul keamanan `Security.php` (Input Filter, Anti-XSS `Security::e`, dan CSRF Protection) serta `RateLimiter.php` (60 req/menit).
* Implementasi awal modul `Auth` (Login & Logout) dan modul `Produk` (CRUD lengkap).
