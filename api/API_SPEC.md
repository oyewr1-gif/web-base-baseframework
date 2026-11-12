# Spesifikasi Teknis API (API Specification)

Dokumen ini berisi rincian teknis *endpoint*, format *request*, kode status HTTP, dan struktur *response* untuk integrasi sisi klien (*Frontend* / *Mobile*).

## Informasi Umum
* **Base URL (Lokal):** `http://localhost/almunaframework/api/`
* **Format Data:** JSON (`application/json`)
* **Mekanisme Otentikasi:** Bearer Token (JWT - HS256)
* **Rate Limiting:** Maksimal **60 request per menit** per alamat IP. Informasi kuota dikirim melalui header `X-RateLimit-Limit`, `X-RateLimit-Remaining`, dan `X-RateLimit-Reset`.

---

## Header Standar
Setiap *request* yang mengirimkan data atau mengakses *endpoint* terproteksi wajib menyertakan *header* berikut:

| Key | Value | Keterangan |
| :--- | :--- | :--- |
| `Content-Type` | `application/json` | Wajib untuk metode `POST` dan `PUT` |
| `Authorization` | `Bearer <token_jwt>` | Wajib untuk seluruh *endpoint* terproteksi |

---

## Kode Status HTTP
API ini menggunakan kode status HTTP standar untuk mengindikasikan hasil *request*:

* `200 OK` - Permintaan berhasil diproses.
* `201 Created` - Data baru berhasil dibuat dan disimpan ke database.
* `400 Bad Request` - Format JSON tidak valid, gagal melewati aturan validasi input, atau tidak ada kolom yang diizinkan (`$fillable`).
* `401 Unauthorized` - Token JWT tidak ditemukan, tidak valid, sudah kedaluwarsa, atau kredensial login salah.
* `404 Not Found` - Data atau *endpoint* yang diminta tidak ditemukan.
* `405 Method Not Allowed` - Metode HTTP (`GET`, `POST`, `PUT`, `DELETE`) tidak sesuai dengan *endpoint*.
* `409 Conflict` - Data unik (seperti *username* atau *email*) sudah terdaftar di sistem.
* `429 Too Many Requests` - Batas kuota permintaan per menit (*Rate Limit*) terlampaui.
* `500 Internal Server Error` - Terjadi kesalahan pada server atau koneksi database.

---

## Modul 1: Otentikasi (`Auth`)

### 1. Registrasi Pengguna Baru
Mendaftarkan akun pengguna baru ke dalam sistem.

* **Endpoint:** `POST /auth/register`
* **Otentikasi:** Publik (Tidak perlu token)
* **Aturan Validasi Payload:**
  * `username`: Wajib diisi, unik, minimal 4 karakter.
  * `email`: Wajib diisi, unik, format email valid.
  * `password`: Wajib diisi, minimal 6 karakter.
* **Contoh Request Body:**
  ```json
  {
    "username": "admin_toko",
    "email": "admin@toko.com",
    "password": "password123"
  }
  ```
* **Contoh Respon Sukses (`201 Created`):**
  ```json
  {
    "status": "success",
    "message": "Registrasi berhasil, silakan login"
  }
  ```
* **Contoh Respon Duplikat (`409 Conflict`):**
  ```json
  {
    "status": "fail",
    "message": "Username sudah terdaftar"
  }
  ```

---

### 2. Login Pengguna
Memverifikasi kredensial pengguna dan menghasilkan token JWT.

* **Endpoint:** `POST /auth/login`
* **Otentikasi:** Publik (Tidak perlu token)
* **Contoh Request Body:**
  ```json
  {
    "username": "admin_toko",
    "password": "password123"
  }
  ```
* **Contoh Respon Sukses (`200 OK`):**
  ```json
  {
    "status": "success",
    "message": "Login berhasil",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 1,
      "username": "admin_toko",
      "email": "admin@toko.com",
      "role": "user"
    }
  }
  ```
* **Contoh Respon Gagal (`401 Unauthorized`):**
  ```json
  {
    "status": "fail",
    "message": "Username atau password salah"
  }
  ```

---

## Modul 2: Produk (`Produk`)

### 1. Ambil Semua Data Produk
Mengambil daftar seluruh produk yang tersimpan di database.

* **Endpoint:** `GET /produk`
* **Otentikasi:** Publik (Dikecualikan via `$exceptAuth`)
* **Contoh Respon Sukses (`200 OK`):**
  ```json
  {
    "status": "success",
    "data": [
      {
        "id": 1,
        "nama": "Laptop Gaming",
        "harga": 15000000,
        "created_at": "2026-11-11 10:00:00"
      }
    ]
  }
  ```

---

### 2. Ambil Detail Satu Produk
Mengambil informasi spesifik produk berdasarkan ID.

* **Endpoint:** `GET /produk/detail/{id}`
* **Otentikasi:** Publik (Dikecualikan via `$exceptAuth`)
* **Parameter Path:** `id` (Integer, Wajib) - ID unik produk.
* **Contoh Respon Sukses (`200 OK`):**
  ```json
  {
    "status": "success",
    "data": {
      "id": 1,
      "nama": "Laptop Gaming",
      "harga": 15000000,
      "created_at": "2026-11-11 10:00:00"
    }
  }
  ```
* **Contoh Respon Gagal (`404 Not Found`):**
  ```json
  {
    "message": "Data tidak ditemukan"
  }
  ```

---

### 3. Tambah Produk Baru
Menyimpan data produk baru ke dalam database. Input otomatis disanitasi dari karakter HTML/XSS.

* **Endpoint:** `POST /produk/store`
* **Otentikasi:** **Wajib** (`Authorization: Bearer <token>`)
* **Kolom Diizinkan (`$fillable`):** `nama`, `harga`
* **Aturan Validasi Payload:**
  * `nama`: Wajib diisi, minimal 3 karakter.
  * `harga`: Wajib diisi, harus berupa angka.
* **Contoh Request Body:**
  ```json
  {
    "nama": "Monitor 24 Inch",
    "harga": 2100000
  }
  ```
* **Contoh Respon Sukses (`201 Created`):**
  ```json
  {
    "status": "success",
    "message": "Data berhasil ditambahkan"
  }
  ```
* **Contoh Respon Gagal Validasi (`400 Bad Request`):**
  ```json
  {
    "status": "fail",
    "message": "Validasi gagal",
    "errors": {
      "nama": [
        "Bidang nama minimal harus memiliki 3 karakter."
      ]
    }
  }
  ```

---

### 4. Perbarui Data Produk
Mengubah sebagian atau seluruh data produk berdasarkan ID. Input otomatis disanitasi dari karakter HTML/XSS.

* **Endpoint:** `PUT /produk/ubah/{id}`
* **Otentikasi:** **Wajib** (`Authorization: Bearer <token>`)
* **Parameter Path:** `id` (Integer, Wajib) - ID unik produk.
* **Kolom Diizinkan (`$fillable`):** `nama`, `harga`
* **Contoh Request Body:**
  ```json
  {
    "nama": "Monitor 27 Inch",
    "harga": 2800000
  }
  ```
* **Contoh Respon Sukses (`200 OK`):**
  ```json
  {
    "status": "success",
    "message": "Data berhasil diperbarui"
  }
  ```

---

### 5. Hapus Data Produk
Menghapus data produk secara permanen dari database.

* **Endpoint:** `DELETE /produk/hapus/{id}`
* **Otentikasi:** **Wajib** (`Authorization: Bearer <token>`)
* **Parameter Path:** `id` (Integer, Wajib) - ID unik produk.
* **Contoh Respon Sukses (`200 OK`):**
  ```json
  {
    "status": "success",
    "message": "Data berhasil dihapus",
    "deleted_by": "admin_toko"
  }
  ```
* **Contoh Respon Gagal Otentikasi (`401 Unauthorized`):**
  ```json
  {
    "status": "fail",
    "message": "Unauthorized: Token JWT wajib disertakan atau sudah kedaluwarsa"
  }
  ```
