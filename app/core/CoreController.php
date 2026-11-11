<?php
abstract class CoreController {
    protected $module;
    protected $viewPath = '';
    protected $redirectPath = '';

    // Mapping input $_POST ke payload JSON API (di-override di child controller)
    protected function mapPayload(array $cleanPost) {
        return $cleanPost;
    }

    protected function view($view, $data = []) {
        extract($data);
        require_once __DIR__ . "/../Views/{$view}.php";
    }

    protected function redirect($path) {
        header("Location: " . rtrim(Env::get('APP_URL'), '/') . $path);
        exit;
    }

    protected function requireAuth() {
        if (empty($_SESSION['jwt_token'])) {
            $this->redirect('/auth/login');
        }
    }

    // --- CRUD TERPUSAT ---
    public function index() {
        $res = $this->module->getAll();
        $this->view("{$this->viewPath}/index", [
            'items' => $res['body']['data'] ?? []
        ]);
    }

    public function create() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf($_POST['csrf_token'] ?? '');
            $cleanPost = Security::cleanInput($_POST);
            $payload = $this->mapPayload($cleanPost);

            $res = $this->module->store($payload);

            if ($res['status_code'] === 201) {
                $this->redirect($this->redirectPath);
            }

            $this->view("{$this->viewPath}/form", [
                'action' => 'create',
                'errors' => $res['body']['errors'] ?? [$res['body']['message'] ?? 'Gagal menyimpan data'],
                'old'    => $cleanPost
            ]);
            return;
        }

        $this->view("{$this->viewPath}/form", ['action' => 'create']);
    }

    public function edit($id) {
        $this->requireAuth();
        $id = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf($_POST['csrf_token'] ?? '');
            $cleanPost = Security::cleanInput($_POST);
            $payload = $this->mapPayload($cleanPost);

            $res = $this->module->update($id, $payload);

            if ($res['status_code'] === 200) {
                $this->redirect($this->redirectPath);
            }

            $this->view("{$this->viewPath}/form", [
                'action' => 'edit',
                'item'   => array_merge(['id' => $id], $cleanPost),
                'errors' => $res['body']['errors'] ?? [$res['body']['message'] ?? 'Gagal memperbarui data']
            ]);
            return;
        }

        $detail = $this->module->getById($id);
        $this->view("{$this->viewPath}/form", [
            'action' => 'edit',
            'item'   => $detail['body']['data'] ?? []
        ]);
    }

    public function delete($id) {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf($_POST['csrf_token'] ?? '');
            $this->module->delete((int) $id);
        }
        $this->redirect($this->redirectPath);
    }
}
