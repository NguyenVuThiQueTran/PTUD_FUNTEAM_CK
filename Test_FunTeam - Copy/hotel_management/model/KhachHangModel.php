<?php
require_once dirname(__FILE__) . '/../config/database.php';

class KhachHangModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // --- SỬA LẠI HÀM NÀY ---
    public function getDanhSachKH() {
        try {
            // Dùng SELECT * để lấy hết các cột (bao gồm CCCD, idKH, hoTen, soDienThoai...)
            $stmt = $this->conn->prepare("SELECT * FROM khachhang ORDER BY idKH DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return array();
        }
    }

    public function themKhachHang($hoTen, $email, $dienThoai, $cccd = '') {
        try {
            // Thêm cột CCCD vào câu lệnh insert
            $stmt = $this->conn->prepare("INSERT INTO khachhang (hoTen, email, soDienThoai, CCCD, loaiKH, trangThai, matKhau) VALUES (?, ?, ?, ?, 'Thuong', 'HoatDong', ?)");
            // Mật khẩu mặc định 123456 (MD5)
            $pass = md5('123456');
            if($stmt->execute(array($hoTen, $email, $dienThoai, $cccd, $pass))) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function suaKhachHang($maKH, $hoTen, $email, $dienThoai, $cccd = '') {
        try {
            $stmt = $this->conn->prepare("UPDATE khachhang SET hoTen=?, email=?, soDienThoai=?, CCCD=? WHERE idKH=?");
            return $stmt->execute(array($hoTen, $email, $dienThoai, $cccd, $maKH));
        } catch(PDOException $e) {
            return false;
        }
    }

    public function xoaKhachHang($maKH) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM khachhang WHERE idKH=?");
            return $stmt->execute(array($maKH));
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>