<?php
class Database {
    private $host = "localhost";
    private $db_name = "quanlykhachsan";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Thử kết nối với utf8mb4 (nếu MySQL cũ không hỗ trợ, sẽ ném PDOException)
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"
            );
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $e) {
            // Nếu server không hỗ trợ utf8mb4 (ví dụ MySQL 5.0), thử lại với utf8
            if (strpos($e->getMessage(), 'Unknown character set') !== false || strpos($e->getMessage(), "SQLSTATE[42000]") !== false) {
                try {
                    $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8";
                    $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'utf8'";
                    $this->conn = new PDO($dsn, $this->username, $this->password, $options);
                } catch(PDOException $ex) {
                    echo "Kết nối thất bại: " . $ex->getMessage();
                    exit;
                }
            } else {
                echo "Kết nối thất bại: " . $e->getMessage();
                exit;
            }
        }
        return $this->conn;
    }
}
?>
