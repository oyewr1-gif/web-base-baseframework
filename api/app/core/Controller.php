<?php
class Controller {
    // Konfigurasi Keamanan JWT (Default: Wajib Login untuk semua endpoint)
    protected $requireAuth = true;
    protected $exceptAuth = []; // Daftar nama method yang bebas diakses tanpa token
    protected $user = null;     // Menyimpan data payload user dari token JWT

    // Konfigurasi CRUD Otomatis
    protected $modelInstance = null;
    protected $validationRules = [];

    public function __construct() {
        // Jalankan pengecekan JWT otomatis saat Controller dipanggil
        $this->checkAuth();
    }

    // --- SISTEM OTENTIKASI OTOMATIS ---
    private function checkAuth() {
        if (!$this->requireAuth) {
            return;
        }

        // Ambil nama method yang sedang dipanggil dari URL
        $url = isset($_GET['url']) ? explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : [];
        $currentMethod = isset($url[1]) ? $url[1] : 'index';

        // Lewati pengecekan jika method termasuk dalam pengecualian ($exceptAuth)
        if (in_array($currentMethod, $this->exceptAuth)) {
            return;
        }

        // Validasi Token JWT
        $userData = JWT::validateAndDecode();
        if (!$userData) {
            $this->response(401, [
                'status'  => 'fail',
                'message' => 'Unauthorized: Token JWT wajib disertakan atau sudah kedaluwarsa'
            ]);
        }

        // Simpan data user agar bisa diakses oleh controller turunan ($this->user)
        $this->user = $userData;
    }

    // --- HELPER DASAR ---
    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model;
    }

    public function response($status_code, $data = []) {
        http_response_code($status_code);
        echo json_encode($data);
        exit;
    }

    // --- METHOD CRUD BAWAAN (OTOMATIS) ---

    // GET /{controller}
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }
        $this->ensureModelExists();

        $data = $this->modelInstance->all();
        $this->response(200, ['status' => 'success', 'data' => $data]);
    }

    // GET /{controller}/detail/{id}
    public function detail($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }
        $this->ensureModelExists();

        if (!$id) $this->response(400, ['message' => 'ID wajib disertakan']);

        $data = $this->modelInstance->find($id);
        if (!$data) $this->response(404, ['message' => 'Data tidak ditemukan']);

        $this->response(200, ['status' => 'success', 'data' => $data]);
    }

    // POST /{controller}/store
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }
        $this->ensureModelExists();

        $rawInput = json_decode(file_get_contents('php://input'), true) ?? [];
        
        // Sanitasi seluruh input untuk mencegah XSS sebelum diproses
        $input = Validator::sanitize($rawInput);

        // Jalankan validasi otomatis jika aturan didefinisikan di controller turunan
        if (!empty($this->validationRules)) {
            $validator = new Validator();
            if (!$validator->validate($input, $this->validationRules)) {
                $this->response(400, [
                    'status'  => 'fail',
                    'message' => 'Validasi gagal',
                    'errors'  => $validator->getErrors()
                ]);
            }
        }

        if ($this->modelInstance->create($input)) {
            $this->response(201, ['status' => 'success', 'message' => 'Data berhasil ditambahkan']);
        } else {
            $this->response(500, ['status' => 'error', 'message' => 'Gagal menyimpan data']);
        }
    }

    // PUT /{controller}/ubah/{id}
    public function ubah($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }
        $this->ensureModelExists();

        if (!$id || !$this->modelInstance->find($id)) {
            $this->response(404, ['message' => 'Data tidak ditemukan']);
        }

        $rawInput = json_decode(file_get_contents('php://input'), true) ?? [];
        
        // Sanitasi seluruh input untuk mencegah XSS sebelum diproses
        $input = Validator::sanitize($rawInput);

        if (empty($input)) {
            $this->response(400, ['message' => 'Tidak ada data yang dikirim untuk diperbarui']);
        }

        if ($this->modelInstance->update($id, $input)) {
            $this->response(200, ['status' => 'success', 'message' => 'Data berhasil diperbarui']);
        } else {
            $this->response(500, ['status' => 'error', 'message' => 'Gagal memperbarui data']);
        }
    }

    // DELETE /{controller}/hapus/{id}
    public function hapus($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }
        $this->ensureModelExists();

        if (!$id || !$this->modelInstance->find($id)) {
            $this->response(404, ['message' => 'Data tidak ditemukan']);
        }

        if ($this->modelInstance->delete($id)) {
            $this->response(200, [
                'status'     => 'success',
                'message'    => 'Data berhasil dihapus',
                'deleted_by' => $this->user['username'] ?? 'unknown'
            ]);
        } else {
            $this->response(500, ['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }

    private function ensureModelExists() {
        if ($this->modelInstance === null) {
            throw new Exception("Model belum diinisialisasi pada Controller ini.");
        }
    }
}
