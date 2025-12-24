<?php
require_once dirname(__FILE__) . '/../config/database.php';

class KhuyenMaiModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getDanhSachKM() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM khuyenmai ORDER BY maKM DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // Sửa lỗi cú pháp mảng tại đây
            return array();
        }
    }

    public function themKhuyenMai($maKM, $tenCT, $mucGiam, $ngayBatDau, $ngayKetThuc) {
        try {
            $sql = "INSERT INTO khuyenmai (maKM, tenCT, mucGiam, ngayBatDau, ngayKetThuc) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute(array($maKM, $tenCT, $mucGiam, $ngayBatDau, $ngayKetThuc));
        } catch(PDOException $e) {
            return false;
        }
    }

    public function suaKhuyenMai($maKM, $tenCT, $mucGiam, $ngayBatDau, $ngayKetThuc) {
        try {
            $sql = "UPDATE khuyenmai SET tenCT=?, mucGiam=?, ngayBatDau=?, ngayKetThuc=? WHERE maKM=?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute(array($tenCT, $mucGiam, $ngayBatDau, $ngayKetThuc, $maKM));
        } catch(PDOException $e) {
            return false;
        }
    }

    public function xoaKhuyenMai($maKM) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM khuyenmai WHERE maKM=?");
            return $stmt->execute(array($maKM));
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function checkMaKM($maKM) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM khuyenmai WHERE maKM = ?");
        $stmt->execute(array($maKM));
        return $stmt->fetchColumn() > 0;
    }
}
?>