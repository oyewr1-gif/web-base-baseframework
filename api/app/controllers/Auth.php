<?php
class Auth extends Controller {
    // Matikan kewajiban token JWT untuk controller otentikasi
    protected $requireAuth = false;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = $this->model('mUser');
    }

    // Endpoint: POST /auth/register
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validasi input dasar
        $validator = new Validator();
        $rules = [
            'username' => ['required', 'min:4'],
            'email'    => ['required'],
            'password' => ['required', 'min:6']
        ];

        if (!$validator->validate($input, $rules)) {
            $this->response(400, [
                'status'  => 'fail',
                'message' => 'Validasi pendaftaran gagal',
                'errors'  => $validator->getErrors()
            ]);
        }

        // Cek format email
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $this->response(400, ['status' => 'fail', 'message' => 'Format email tidak valid']);
        }

        // Cek duplikasi username atau email
        if ($this->userModel->findByUsername($input['username'])) {
            $this->response(409, ['status' => 'fail', 'message' => 'Username sudah terdaftar']);
        }

        if ($this->userModel->findByEmail($input['email'])) {
            $this->response(409, ['status' => 'fail', 'message' => 'Email sudah terdaftar']);
        }

        // Enkripsi password sebelum disimpan ke database
        $hashedPassword = password_hash($input['password'], PASSWORD_BCRYPT);

        $payload = [
            'username' => htmlspecialchars(strip_tags($input['username'])),
            'email'    => htmlspecialchars(strip_tags($input['email'])),
            'password' => $hashedPassword,
            'role'     => 'user'
        ];

        if ($this->userModel->create($payload)) {
            $this->response(201, [
                'status'  => 'success',
                'message' => 'Registrasi berhasil, silakan login'
            ]);
        } else {
            $this->response(500, ['status' => 'error', 'message' => 'Gagal memproses registrasi']);
        }
    }

    // Endpoint: POST /auth/login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->response(405, ['message' => 'Method Not Allowed']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $validator = new Validator();
        $rules = [
            'username' => ['required'],
            'password' => ['required']
        ];

        if (!$validator->validate($input, $rules)) {
            $this->response(400, [
                'status'  => 'fail',
                'message' => 'Username dan password wajib diisi',
                'errors'  => $validator->getErrors()
            ]);
        }

        // Cari pengguna di database
        $user = $this->userModel->findByUsername($input['username']);

        // Verifikasi kecocokan password
        if (!$user || !password_verify($input['password'], $user['password'])) {
            $this->response(401, [
                'status'  => 'fail',
                'message' => 'Username atau password salah'
            ]);
        }

        // Susun payload untuk disematkan di dalam token JWT
        $tokenPayload = [
            'user_id'  => $user['id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

        // Hasilkan token JWT asli
        $token = JWT::generate($tokenPayload);

        $this->response(200, [
            'status'  => 'success',
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => [
                'id'       => $user['id'],
                'username' => $user['username'],
                'email'    => $user['email'],
                'role'     => $user['role']
            ]
        ]);
    }
}
