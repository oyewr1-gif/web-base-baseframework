# Changelog

Semua perubahan penting pada proyek **REST API PHP MVC Native** ini akan dicatat di dalam file ini.

## - 2026-11-11
### Ditambahkan
* **Pencegahan Mass Assignment (`\(fillable`):** Penyaringan kolom yang diizinkan serta validasi regex nama kolom pada `Model.php` untuk mencegah injeksi kolom berbahaya. * **Sanitasi Input Rekursif (`Validator::sanitize`):** Pembersihan otomatis tag HTML dan karakter spesial pada seluruh *payload* JSON untuk mencegah serangan XSS. * **Rate Limiting (`RateLimiter.php`):** Pembatasan kuota akses maksimal 60 *request* per menit per IP menggunakan penyimpanan cache sementara untuk mencegah serangan *Brute Force* dan *DDoS* ringan.  ### Diperbarui * **Keamanan JWT (`JWT.php`):** Mengganti perbandingan string tanda tangan digital dengan fungsi `hash_equals()` untuk mencegah *Timing Attack*, serta dukungan pembacaan *header* `Authorization` secara *case-insensitive*.  ## - 2026-11-11 ### Ditambahkan * **Proteksi JWT Otomatis pada Core Controller (`Controller.php`):** Seluruh *endpoint* pada setiap modul baru otomatis terkunci oleh JWT secara *default* dengan opsi pengecualian melalui `\)exceptAuth` atau `\(requireAuth`. * **CRUD Bawaan pada Core Controller:** Sentralisasi *method* `index()`, `detail()`, `store()`, `ubah()`, dan `hapus()` beserta validasi input otomatis agar tidak terjadi duplikasi kode antar modul.  ## - 2026-11-11 ### Ditambahkan * **Modul Otentikasi (`Auth.php` & `User_model.php`):** Endpoint `POST /auth/register` (dengan enkripsi `PASSWORD_BCRYPT`) dan `POST /auth/login` yang menghasilkan token JWT asli. * **File Kolaborasi Git:** Penambahan `.env.example` dan `.gitignore` untuk keamanan kredensial tim. * **Dokumentasi Proyek:** Pembuatan `README.md`, `API_SPEC.md`, dan `CHANGELOG.md`.  ### Diperbaiki * **Konfigurasi `.htaccess`:** Penambahan aturan `RewriteRule ^\) public/index.php [L]` dan penerusan parameter `url=\$1` serta *header* `HTTP_AUTHORIZATION` untuk mencegah error `403 Forbidden` saat menggunakan folder `public/`.

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
