<?php
class Produk extends Controller {
    // Bebaskan akses publik hanya untuk melihat daftar dan detail produk
    protected $exceptAuth = ['index', 'detail'];

    // Aturan validasi otomatis saat method store() dipanggil
    protected $validationRules = [
        'nama'  => ['required', 'min:3'],
        'harga' => ['required', 'numeric']
    ];

    public function __construct() {
        parent::__construct(); // Wajib dipanggil agar proteksi JWT aktif
        $this->modelInstance = $this->model('mProduk');
    }

    // Method index(), detail(), store(), ubah(), dan hapus() 
    // sudah otomatis tersedia dari Core Controller!
}
