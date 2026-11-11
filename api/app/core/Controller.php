<?php
class Controller {
    public function model($model) {
        require_once APP_PATH . 'models/' . $model . '.php';
        return new $model;
    }

    // Menggantikan fungsi "View" konvensional dengan output JSON
    public function response($status_code, $data = []) {
        http_response_code($status_code);
        echo json_encode($data);
        exit;
    }
}
