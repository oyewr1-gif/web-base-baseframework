<?php
class mProduk extends Model {
    protected $table = 'produk'; 
    
    // Tentukan cetakan (skema) SQL untuk tabel ini jika belum ada
    protected $schema = "
        CREATE TABLE IF NOT EXISTS produk (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(255) NOT NULL,
            harga INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    
}
