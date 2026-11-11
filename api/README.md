# REST API PHP MVC Native

Proyek ini adalah kerangka kerja (*framework*) REST API ringan berbasis **PHP Native** dengan pola arsitektur **MVC (Model-View-Controller)**. Dibangun tanpa ketergantungan pustaka pihak ketiga untuk inti aplikasi, serta dilengkapi dengan fitur keamanan dan otomasi modern.

## Fitur Utama
* **Arsitektur MVC Bersih:** Pemisahan logika bisnis, routing, dan manipulasi data.
* **Auto-Database & Table Migration:** Membuat database dan tabel secara otomatis saat pertama kali dijalankan jika belum tersedia.
* **Environment Configuration (`.env`):** Pengelolaan kredensial sensitif secara aman tanpa *hardcode*.
* **Otentikasi JWT (JSON Web Token):** Keamanan *stateless* berbasis algoritma HS256.
* **Validasi Input Otomatis:** Memvalidasi *payload* JSON sebelum diproses ke database.
* **Global Error Handler:** Menangkap *Exception*, *Warning*, dan *Fatal Error* menjadi respon JSON yang rapi dan aman untuk produksi.

---

## Struktur Direktori

```text
my-api/
├── app/
│   ├── core/
│   │   ├── App.php           # Sistem Routing URL utama
│   │   ├── Controller.php    # Base Controller, Proteksi JWT Otomatis & CRUD Bawaan
│   │   ├── DotEnv.php        # Pembaca konfigurasi file .env
│   │   ├── ErrorHandler.php  # Penangkap error global ke format JSON
│   │   ├── JWT.php           # Generator & Validator token JWT (HS256 + hash_equals)
│   │   ├── Model.php         # Base Model (PDO, Auto-Migration, Filter \$fillable & CRUD)
│   │   ├── RateLimiter.php   # Pembatasan kuota request per IP (Anti Brute-Force)
│   │   └── Validator.php     # Mesin validasi & sanitasi input (Anti-XSS)
│   │
│   ├── controllers/
│   │   ├── Auth.php          # Endpoint registrasi & login pengguna
│   │   └── Produk.php        # Endpoint manajemen data produk
│   │
│   ├── models/
│   │   ├── Produk_model.php  # Skema tabel, \$fillable & model data produk
│   │   └── User_model.php    # Skema tabel, \$fillable & model data pengguna
│   │
│   └── init.php              # Pemuat terpusat semua komponen core aplikasi
│
├── public/
│   └── index.php             # Single Entry Point & Konfigurasi CORS
│
├── tests/                    # Direktori pengujian otomatis (PHPUnit)
│   ├── JWTTest.php
│   └── ValidatorTest.php
│
├── .env                      # Konfigurasi environment lokal (Diabaikan Git)
├── .env.example              # Templat konfigurasi environment untuk tim
├── .gitignore                # Daftar file/folder yang diabaikan Git
├── .htaccess                 # URL Rewriting Apache & penerus header Authorization
├── API_SPEC.md               # Spesifikasi teknis endpoint untuk tim Frontend/Mobile
├── CHANGELOG.md              # Catatan riwayat perubahan versi proyek
└── README.md                 # Dokumentasi utama proyek
`

---

## Instalasi & Konfigurasi

1. **Clone atau Salin Proyek**
   Letakkan folder proyek di dalam direktori web server lokal Anda (misal: `htdocs/` pada XAMPP atau `www/` pada Laragon).

2. **Konfigurasi Environment (`.env`)**
   Buat file `.env` di direktori utama (`my-api/.env`) dan sesuaikan dengan pengaturan server Anda:
   ```ini
   APP_ENV=development

   DB_HOST=localhost
   DB_NAME=nama_database_anda
   DB_USER=root
   DB_PASS=

   JWT_SECRET=kunci_rahasia_super_aman_12345
   JWT_EXPIRY=3600
   ```
   > **Catatan:** Ubah `APP_ENV=production` saat aplikasi sudah siap dipublikasikan agar detail *stack trace* error disembunyikan dari publik.

3. **Inisialisasi Database**
   Anda tidak perlu membuat database atau tabel secara manual. Saat endpoint diakses pertama kali, `Model.php` akan otomatis membuat database dan tabel berdasarkan properti `$schema` pada masing-masing Model.

---

## Dokumentasi Endpoint API

**Base URL:** `http://localhost/my-api/public` (atau sesuaikan dengan *Virtual Host* lokal Anda).

### 1. Menampilkan Semua Produk
* **URL:** `/produk`
* **Method:** `GET`
* **Auth:** Tidak diperlukan
* **Respon Sukses (`200 OK`):**
  ```json
  {
    "status": "success",
    "data": [
      {
        "id": 1,
        "nama": "Laptop",
        "harga": 15000000,
        "created_at": "2026-11-11 10:00:00"
      }
    ]
  }
  ```

### 2. Menampilkan Detail Produk
* **URL:** `/produk/detail/{id}`
* **Method:** `GET`
* **Auth:** Tidak diperlukan
* **Respon Sukses (`200 OK`):**
  ```json
  {
    "status": "success",
    "data": {
      "id": 1,
      "nama": "Laptop",
      "harga": 15000000
    }
  }
  ```

### 3. Menambah Produk Baru
* **URL:** `/produk/store`
* **Method:** `POST`
* **Headers:** `Content-Type: application/json`
* **Body (Raw JSON):**
  ```json
  {
    "nama": "Keyboard Mekanikal",
    "harga": 850000
  }
  ```
* **Respon Sukses (`201 Created`):**
  ```json
  {
    "message": "Produk berhasil ditambahkan"
  }
  ```
* **Respon Gagal Validasi (`400 Bad Request`):**
  ```json
  {
    "status": "fail",
    "message": "Validasi gagal",
    "errors": {
      "nama": ["Bidang nama wajib diisi."]
    }
  }
  ```

### 4. Memperbarui Produk
* **URL:** `/produk/ubah/{id}`
* **Method:** `PUT`
* **Headers:** `Content-Type: application/json`
* **Body (Raw JSON):**
  ```json
  {
    "harga": 900000
  }
  ```
* **Respon Sukses (`200 OK`):**
  ```json
  {
    "message": "Produk berhasil diperbarui"
  }
  ```

### 5. Menghapus Produk
* **URL:** `/produk/hapus/{id}`
* **Method:** `DELETE`
* **Headers:** `Authorization: Bearer <token_jwt_anda>`
* **Respon Sukses (`200 OK`):**
  ```json
  {
    "status": "success",
    "message": "Produk berhasil dihapus",
    "deleted_by": "username_admin"
  }
  ```
* **Respon Tanpa Token (`401 Unauthorized`):**
  ```json
  {
    "status": "fail",
    "message": "Unauthorized: Token tidak valid atau kedaluwarsa"
  }
  ```

---


## Panduan Pengembangan Modul Baru (Untuk Tim)

Kerangka kerja ini menerapkan prinsip **Secure by Default**:
1. **Model Baru:** Wajib mewarisi `Model`, mendefinisikan `$table`, `$schema`, dan daftar kolom yang diizinkan pada array `$fillable` untuk mencegah *Mass Assignment*.
2. **Controller Baru:** Wajib mewarisi `Controller` dan memanggil `parent::__construct()`. Secara *default*, seluruh *endpoint* terkunci oleh token JWT. Gunakan properti `protected $exceptAuth = ['index', 'detail'];` jika ingin membuka akses publik pada *method* tertentu.

---

## Panduan Pengujian (Testing)

### Pengujian Manual (Postman)
1. Gunakan aplikasi HTTP Client seperti Postman.
2. **Penting:** Pastikan pengaturan **Automatically follow redirects** di menu *Settings* Postman dalam keadaan **OFF** jika menggunakan *Virtual Host* (`.local`), agar metode `POST`/`PUT`/`DELETE` tidak berubah menjadi `GET` saat terjadi *redirect* server.

### Pengujian Otomatis (PHPUnit)
Untuk menjalankan *Unit Testing* pada komponen *Validator* dan *JWT*:
1. Pastikan Composer terinstal, lalu jalankan:
   ```bash
   composer require --dev phpunit/phpunit
   ```
2. Jalankan pengujian melalui terminal:
   ```bash
   ./vendor/bin/phpunit tests
   ```
