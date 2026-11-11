<?php
class ProdukModule extends CoreModule {
    protected $endpoint = '/produk';
    // GET /produk dan GET /produk/detail/{id} bersifat publik ($exceptAuth)
    // POST, PUT, DELETE otomatis membutuhkan JWT sesuai default CoreModule
}
