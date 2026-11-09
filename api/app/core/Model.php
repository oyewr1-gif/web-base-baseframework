<?php
class Model {
    protected $db;
    protected $table;
    protected $schema; // Properti baru untuk menampung query pembuat tabel

    public function __construct() {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->db = new PDO($dsn, $user, $pass, $options);
            
            // Pemicu inisialisasi tabel otomatis
            $this->initTable(); 
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
            exit;
        }
    }

    // Fungsi baru untuk membuat tabel secara otomatis jika belum ada
    private function initTable() {
        if (!empty($this->table) && !empty($this->schema)) {
            try {
                $this->db->exec($this->schema);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["message" => "Gagal menginisialisasi tabel '{$this->table}': " . $e->getMessage()]);
                exit;
            }
        }
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $fields = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $querySet = "";
        foreach ($data as $key => $value) {
            $querySet .= "$key = :$key, ";
        }
        $querySet = rtrim($querySet, ", ");

        $sql = "UPDATE {$this->table} SET $querySet WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
