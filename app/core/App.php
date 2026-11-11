<?php
class App {
    protected $module = 'Produk';
    protected $controller = 'ProdukController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        if (!empty($url[0])) {
            $modName = ucfirst(strtolower($url[0]));
            $ctrlPath = __DIR__ . "/../Modules/{$modName}/{$modName}Controller.php";
            if (file_exists($ctrlPath)) {
                $this->module = $modName;
                $this->controller = "{$modName}Controller";
                unset($url[0]);
            }
        }

        require_once __DIR__ . "/../Modules/{$this->module}/{$this->controller}.php";
        $this->controller = new $this->controller;

        if (!empty($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        $this->params = $url ? array_values($url) : [];
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
