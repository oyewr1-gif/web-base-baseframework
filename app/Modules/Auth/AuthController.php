<?php
require_once __DIR__ . '/AuthModule.php';

class AuthController extends CoreController {
    public function __construct() {
        $this->module = new AuthModule();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf($_POST['csrf_token'] ?? '');
            $clean = Security::cleanInput($_POST);

            $res = $this->module->login($clean['username'] ?? '', $_POST['password'] ?? '');

            if ($res['status_code'] === 200) {
                session_regenerate_id(true);
                $_SESSION['jwt_token'] = $res['body']['token'];
                $_SESSION['user']      = $res['body']['user'];
                $this->redirect('/produk');
            }

            $this->view('auth/login', [
                'error' => $res['body']['message'] ?? 'Username atau password salah'
            ]);
            return;
        }

        $this->view('auth/login');
    }

    public function logout() {
        session_unset();
        session_destroy();
        $this->redirect('/auth/login');
    }
}
