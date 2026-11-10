<?php
class App {
    protected $controller = 'Produk';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseURL();
        
        // Cek apakah file controller ada
        if (isset($url[0]) && file_exists('../app/controllers/' . ucfirst($url[0]) . '.php')) {
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
        }

        require_once APP_PATH . 'controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Cek apakah method ada
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Ambil sisa parameter jika ada
        $this->params = $url ? array_values($url) : [];

        // Jalankan controller & method serta kirimkan params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
