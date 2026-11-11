<?php
use PHPUnit\Framework\TestCase;

// Muat manual core validator karena kita tidak pakai autoloader composer untuk app kita
require_once __DIR__ . '/../app/core/Validator.php';

class ValidatorTest extends TestCase {
    
    public function testValidasiBerhasilJikaInputBenar() {
        $validator = new Validator();
        $input = ['nama' => 'Laptop ASUS', 'harga' => 12000000];
        $rules = [
            'nama'  => ['required', 'min:3'],
            'harga' => ['required', 'numeric']
        ];

        $isValid = $validator->validate($input, $rules);
        
        // Memastikan hasilnya harus TRUE
        $this->assertTrue($isValid);
        $this->assertEmpty($validator->getErrors());
    }

    public function testValidasiGagalJikaInputKosong() {
        $validator = new Validator();
        $input = ['nama' => '', 'harga' => ''];
        $rules = [
            'nama'  => ['required'],
            'harga' => ['required']
        ];

        $isValid = $validator->validate($input, $rules);
        
        // Memastikan hasilnya harus FALSE
        $this->assertFalse($isValid);
        $this->assertArrayHasKey('nama', $validator->getErrors());
    }
}
