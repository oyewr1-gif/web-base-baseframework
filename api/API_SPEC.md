# Spesifikasi Teknis API (API Specification)

Dokumen ini berisi rincian teknis *endpoint*, format *request*, kode status HTTP, dan struktur *response* untuk integrasi sisi klien (*Frontend* / *Mobile*).

## Informasi Umum
* **Base URL (Lokal):** `http://localhost/my-api/public`
* **Format Data:** JSON (`application/json`)
* **Mekanisme Otentikasi:** Bearer Token (JWT - HS256)

---

## Header Standar
Setiap *request* yang mengirimkan data atau mengakses *endpoint* terproteksi wajib menyertakan *header* berikut:

| Key | Value | Keterangan |
| :--- | :--- | :--- |
| `Content-Type` | `application/json` | Wajib untuk metode `POST` dan `PUT` |
| `Authorization` | `Bearer <token_jwt>` | Wajib untuk *endpoint* terproteksi |

---

## Kode Status HTTP
API ini menggunakan kode status HTTP standar untuk mengindikasikan hasil *request*:

* `200 OK` - Permintaan berhasil diproses.
* `201 Created` - Data baru berhasil dibuat dan disimpan ke database.
* `400 Bad Request` - Format JSON tidak valid atau gagal melewati aturan validasi input.
* `401 Unauthorized` - Token JWT tidak ditemukan, tidak valid, atau sudah kedaluwarsa.
* `404 Not Found` - Data atau *endpoint* yang diminta tidak ditemukan.
* `405 Method Not Allowed` - Metode HTTP (`GET`, `POST`, `PUT`, `DELETE`) tidak sesuai dengan *endpoint*.
* `500 Internal Server Error` - Terjadi kesalahan pada server atau koneksi database.

---

## Daftar Endpoint: Modul Produk

### 1. Ambil Semua Data Produk
Mengambil daftar seluruh produk yang tersimpan di database.

* **Endpoint:** `GET /produk`
* **Otentikasi:** Publik (Tidak perlu token)
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
      },
      {
        "id": 2,
        "nama": "Mouse Wireless",
        "harga": 250000,
        "created_at": "2026-11-11 10:05:00"
      }
    ]
  }
  ```

---

### 2. Ambil Detail Satu Produk
Mengambil informasi spesifik produk berdasarkan ID.

* **Endpoint:** `GET /produk/detail/{id}`
* **Otentikasi:** Publik (Tidak perlu token)
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
    "message": "Produk tidak ditemukan"
  }
  ```

---

### 3. Tambah Produk Baru
Menyimpan data produk baru ke dalam database.

* **Endpoint:** `POST /produk/store`
* **Otentikasi:** Publik (Dapat disesuaikan menjadi terproteksi)
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
    "message": "Produk berhasil ditambahkan"
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
      ],
      "harga": [
        "Bidang harga harus berupa angka."
      ]
    }
  }
  ```

---

### 4. Perbarui Data Produk
Mengubah sebagian atau seluruh data produk berdasarkan ID.

* **Endpoint:** `PUT /produk/ubah/{id}`
* **Otentikasi:** Publik (Dapat disesuaikan menjadi terproteksi)
* **Parameter Path:** `id` (Integer, Wajib) - ID unik produk.
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
    "message": "Produk berhasil diperbarui"
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
    "message": "Produk berhasil dihapus",
    "deleted_by": "admin_user"
  }
  ```
* **Contoh Respon Gagal Otentikasi (`401 Unauthorized`):**
  ```json
  {
    "status": "fail",
    "message": "Unauthorized: Token tidak valid atau kedaluwarsa"
  }
  ```
