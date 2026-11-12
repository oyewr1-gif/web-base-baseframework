<?php
require_once __DIR__ . '/ProdukModule.php';

class ProdukController extends CoreController {
    protected $viewPath = 'produk';
    protected $redirectPath = '/produk';

    public function __construct() {
        $this->module = new ProdukModule();
    }

    // Sesuaikan tipe data dengan aturan validasi API ($fillable: nama, harga)
    protected function mapPayload(array $cleanPost) {
        return [
            'nama'  => $cleanPost['nama'] ?? '',
            'harga' => isset($cleanPost['harga']) ? (int) $cleanPost['harga'] : 0
        ];
    }
}
