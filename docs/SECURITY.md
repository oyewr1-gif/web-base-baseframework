# Kebijakan & Implementasi Keamanan

Framework ini menerapkan pendekatan *Defense in Depth* untuk melindungi komunikasi antara klien, aplikasi front-end, dan server REST API.

## 1. Manajemen Token JWT
* **Penyimpanan Sisi Server:** Token JWT yang diterima dari endpoint `POST /auth/login` disimpan di dalam variabel `$_SESSION['jwt_token']`, bukan di `localStorage` atau *cookie* sisi klien yang dapat dibaca JavaScript.
* **Perlindungan Cookie Sesi:** Sesi dikonfigurasi menggunakan `cookie_httponly => true` dan `cookie_samesite => 'Strict'`.
* **Regenerasi ID Sesi:** Setiap proses login berhasil memanggil `session_regenerate_id(true)` untuk mencegah serangan *Session Fixation*.
* **Auto-Logout pada 401:** Jika API mengembalikan status `401 Unauthorized` (token kedaluwarsa/tidak valid), `ApiClient` otomatis menghapus sesi token lokal.

## 2. Input Filtering & Validasi
* Seluruh data masuk dari `$_POST` dan parameter URL disaring melalui `Security::cleanInput()` untuk menghapus spasi berlebih, *slashes*, dan tag HTML berbahaya sebelum diproses lebih lanjut.
* Tipe data numerik (seperti `harga` dan `id`) dikonversi secara eksplisit menggunakan *type-casting* `(int)` di lapisan controller.

## 3. Pencegahan Cross-Site Scripting (XSS)
* Seluruh data dinamis yang ditampilkan pada file di dalam `app/Views/` wajib dibungkus dengan helper `Security::e($string)` yang menggunakan `htmlspecialchars()` dengan flag `ENT_QUOTES | ENT_HTML5` dan encoding `UTF-8`.

## 4. Pencegahan Cross-Site Request Forgery (CSRF)
* Setiap operasi yang mengubah data (`POST` untuk tambah, ubah, dan hapus data) wajib menyertakan input tersembunyi `csrf_token` dari `Security::csrfToken()`.
* Server memverifikasi token tersebut menggunakan perbandingan waktu konstan (`hash_equals`) melalui `Security::verifyCsrf()`.

## 5. Rate Limiting
* Kelas `RateLimiter` membatasi jumlah permintaan per alamat IP sesuai konfigurasi `RATE_LIMIT_MAX` (bawaan: **60 request per 60 detik**), selaras dengan kebijakan *Rate Limiting* pada server REST API.
* Jika kuota terlampaui, aplikasi merespons dengan status HTTP `429 Too Many Requests` beserta header `Retry-After`.
