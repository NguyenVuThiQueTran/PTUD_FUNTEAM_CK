<?php
class clsKetNoi{
    private $conn;
    
    public function moketnoi() {
        // SỬA: BỎ DẤU { SAU new mysqli(...)
        $this->conn = new mysqli("localhost", "root", "", "quanlykhachsan");
        
        // Set charset UTF-8
        $this->conn->set_charset("utf8");
        $this->conn->query("SET NAMES 'utf8mb4'");
        $this->conn->query("SET CHARACTER SET utf8mb4");
        $this->conn->query("SET character_set_connection = utf8mb4");
        
        // Kiểm tra lỗi kết nối
        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }
        
        return $this->conn;
    }

    public function dongketnoi() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>