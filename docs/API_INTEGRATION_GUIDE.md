# Panduan Integrasi REST API (`API_INTEGRATION_GUIDE.md`)

Dokumen ini merupakan referensi pemetaan teknis antara lapisan **Front-End PHP MVC** dengan **Almuna REST API** (`http://localhost/almunaframework/api/`).

---

## 1. Standar Komunikasi & Header

Seluruh komunikasi antara `ApiClient.php` dan server API menggunakan format pertukaran data **JSON** dengan aturan *header* berikut:

| Header | Nilai | Keterangan & Penerapan di `ApiClient` |
| :--- | :--- | :--- |
| `Accept` | `application/json` | Dikirim pada setiap *request* untuk memastikan respons berformat JSON. |
| `Content-Type` | `application/json` | Otomatis ditambahkan pada metode `POST` dan `PUT`. |
| `Authorization` | `Bearer <jwt_token>` | Diambil dari `$_SESSION['jwt_token']` untuk *endpoint* yang membutuhkan otentikasi. |

---

## 2. Tabel Pemetaan Route Front-End ke Endpoint API

### A. Modul Otentikasi (`Auth`)

| Route Front-End | Controller & Method | Endpoint API | Metode HTTP | Auth JWT | Payload JSON |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/auth/register` | `AuthController::register()` | `/auth/register` | `POST` | Publik (Tidak) | `username`, `email`, `password` |
| `/auth/login` | `AuthController::login()` | `/auth/login` | `POST` | Publik (Tidak) | `username`, `password` |
| `/auth/logout` | `AuthController::logout()` | *(Lokal Sesi)* | `GET` | - | Menghapus `$_SESSION` lokal |

### B. Modul Produk (`Produk`)

| Route Front-End | Controller & Method | Endpoint API | Metode HTTP | Auth JWT | Payload JSON |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/produk` | `ProdukController::index()` | `/produk` | `GET` | Publik (`$exceptAuth`) | - |
| `/produk/detail/{id}` | `ProdukController::detail($id)` | `/produk/detail/{id}` | `GET` | Publik (`$exceptAuth`) | - |
| `/produk/create` | `ProdukController::create()` | `/produk/store` | `POST` | **Wajib** | `nama` (string), `harga` (int) |
| `/produk/edit/{id}` | `ProdukController::edit($id)` | `/produk/ubah/{id}` | `PUT` | **Wajib** | `nama` (string), `harga` (int) |
| `/produk/delete/{id}` | `ProdukController::delete($id)` | `/produk/hapus/{id}` | `DELETE` | **Wajib** | - |

> **Catatan Metode HTTP:** Browser HTML hanya mendukung metode `GET` dan `POST` pada tag `<form>`. Oleh karena itu, `CoreController` menerima `POST` dari browser, lalu `CoreModule` dan `ApiClient` menerjemahkannya menjadi metode `PUT` atau `DELETE` saat memanggil REST API.

---

## 3. Penanganan Kode Status HTTP (`HTTP Status Codes`)

Setiap respons dari API dikembalikan oleh `ApiClient::request()` dalam array asosiatif `['status_code' => int, 'body' => array]`. Berikut adalah standar penanganannya di sisi front-end:

| Kode Status | Arti Menurut `API_SPEC` | Tindakan pada Front-End |
| :--- | :--- | :--- |
| **`200 OK`** | Permintaan berhasil diproses (Login, Get, Update, Delete). | Simpan token (jika login) atau lakukan *redirect* ke halaman indeks. |
| **`201 Created`** | Data baru berhasil dibuat (Register, Store Produk). | Arahkan pengguna ke halaman login atau daftar produk. |
| **`400 Bad Request`** | Validasi input gagal, format JSON salah, atau kolom di luar `$fillable`. | Render ulang form dengan menampilkan pesan detail pada array `body['errors']`. |
| **`401 Unauthorized`** | Token JWT kosong, tidak valid, kedaluwarsa, atau login gagal. | `ApiClient` menghapus `$_SESSION['jwt_token']` dan mengarahkan ke `/auth/login`. |
| **`404 Not Found`** | Data dengan ID tertentu atau *endpoint* tidak ditemukan. | Tampilkan pesan *"Data tidak ditemukan"* atau halaman 404. |
| **`405 Method Not Allowed`** | Metode HTTP tidak sesuai dengan definisi *endpoint*. | Periksa konfigurasi metode pada `CoreModule`. |
| **`409 Conflict`** | Data unik (`username` atau `email`) sudah terdaftar saat registrasi. | Tampilkan `body['message']` pada form registrasi. |
| **`429 Too Many Requests`** | Kuota **60 request/menit** per IP terlampaui. | Ditahan lebih awal oleh `RateLimiter.php` sebelum membebani API. |
| **`500 Internal Server Error`** | Kesalahan internal server API atau koneksi database. | Tampilkan pesan gangguan layanan sementara kepada pengguna. |

---

## 4. Aturan Validasi & Pemetaan Payload (`$fillable`)

Untuk mencegah respons `400 Bad Request` dari API, pastikan setiap controller turunan mengimplementasikan `mapPayload()` agar hanya mengirim kolom yang diizinkan (`$fillable`) dengan tipe data yang sesuai:

* **Registrasi (`POST /auth/register`):**
  * `username`: Wajib diisi, minimal 4 karakter.
  * `email`: Wajib diisi, format email valid.
  * `password`: Wajib diisi, minimal 6 karakter.
* **Produk (`POST /produk/store` & `PUT /produk/ubah/{id}`):**
  * `nama`: Wajib diisi, minimal 3 karakter (`string`).
  * `harga`: Wajib diisi, wajib bertipe numerik (`(int)`).
