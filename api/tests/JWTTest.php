<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/core/JWT.php';

class JWTTest extends TestCase {
    
    protected function setUp(): void {
        // Set environment variable tiruan khusus untuk testing
        $_ENV['JWT_SECRET'] = 'test_secret_key_12345';
        $_ENV['JWT_EXPIRY'] = '3600';
    }

    public function testGenerateTokenMenghasilkanStringTigaBagian() {
        $payload = ['user_id' => 1, 'username' => 'admin'];
        $token = JWT::generate($payload);

        $this->assertIsString($token);
        
        // JWT harus memiliki 3 bagian yang dipisahkan oleh tanda titik (.)
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);
    }
}
