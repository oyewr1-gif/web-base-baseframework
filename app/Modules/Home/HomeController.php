<?php
class HomeController extends CoreController {
    public function index() {
        $this->view('home/index', [
            'title' => 'Beranda'
        ]);
    }
}
