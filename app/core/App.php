<?php
class App {
    protected $module = 'Home';
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();
        //echo "App.php";

         // 1. Cek segmen pertama ($url[0]) sebagai nama Modul & Controller
        if (!empty($url[0])) {
            $modName = ucfirst(strtolower($url[0]));
            $ctrlPath = APP_PATH . "/Modules/{$modName}/{$modName}Controller.php";
            if (file_exists($ctrlPath)) {
                $this->module = $modName;
                $this->controller = "{$modName}Controller";
                unset($url[0]);
            }
        }

        // Instansiasi Controller (default: ProdukController)
        //require_once __DIR__ . "/../Modules/{$this->module}/{$this->controller}.php";
        $this->controller = new $this->controller;

        // 2. Cek segmen kedua ($url[1]) sebagai nama Method
        if (!empty($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        // 3. Sisa segmen menjadi parameter
        $this->params = $url ? array_values($url) : [];
        // Jalankan controller & method
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            $cleanUrl = Security::cleanInput(rtrim($_GET['url'], '/'));
            return explode('/', filter_var($cleanUrl, FILTER_SANITIZE_URL));
        }
        return [];
    }
}
