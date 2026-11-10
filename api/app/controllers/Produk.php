<?php
class Produk extends Controller {
    private $produkModel;

    public function __construct() {
        $this->produkModel = $this->model('mProduk');
    }

    // GET /produk - Mengambil semua data
    public function index() {
        //echo "Produk\r\n"; 
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }

        $data = $this->produkModel->all();
        $this->response(200, ['status' => 'success', 'data' => $data]);
    }

    // GET /produk/detail/{id} - Mengambil satu data
    public function detail($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }

        if (!is_numeric($id)) {
            throw new Exception("ID Produk harus berupa angka!", 400); 
            // Catatan: Jika ingin status HTTP mengikuti kode exception (400),
            // Anda bisa membuat Custom Exception Class, namun default-nya akan ditangkap sebagai 500 oleh ErrorHandler jika menggunakan Exception bawaan.
        }
        
        $data = $this->produkModel->find($id);
        if ($data) {
            $this->response(200, ['status' => 'success', 'data' => $data]);
        } else {
            throw new Exception("Produk dengan ID $id tidak ditemukan.");
            $this->response(404, ['message' => 'Produk tidak ditemukan']);
        }
    }

    // POST /produk/store - Menambah data produk baru
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }

        // Mengambil input berformat JSON raw dari body request
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['nama']) || !isset($input['harga'])) {
            $this->response(400, ['message' => 'Bad Request: Data tidak lengkap']);
        }

        $payload = [
            'nama' => $input['nama'],
            'harga' => $input['harga']
        ];

        if ($this->produkModel->create($payload)) {
            $this->response(201, ['message' => 'Produk berhasil ditambahkan']);
        } else {
            $this->response(500, ['message' => 'Gagal menyimpan produk']);
        }
    }

    // PUT /produk/ubah/{id} - Mengubah data produk
    public function ubah($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }

        $produk = $this->produkModel->find($id);
        if (!$produk) {
            $this->response(404, ['message' => 'Produk tidak ditemukan']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        // Satukan data yang ingin diperbarui
        $payload = [];
        if (isset($input['nama'])) $payload['nama'] = $input['nama'];
        if (isset($input['harga'])) $payload['harga'] = $input['harga'];

        if (empty($payload)) {
            $this->response(400, ['message' => 'Tidak ada data yang diubah']);
        }

        if ($this->produkModel->update($id, $payload)) {
            $this->response(200, ['message' => 'Produk berhasil diperbarui']);
        } else {
            $this->response(500, ['message' => 'Gagal memperbarui produk']);
        }
    }

    // DELETE /produk/hapus/{id} - Menghapus data produk
    public function hapus($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }

        $produk = $this->produkModel->find($id);
        if (!$produk) {
            $this->response(404, ['message' => 'Produk tidak ditemukan']);
        }

        if ($this->produkModel->delete($id)) {
            $this->response(200, ['message' => 'Produk berhasil dihapus']);
        } else {
            $this->response(500, ['message' => 'Gagal menghapus produk']);
        }
    }
}
