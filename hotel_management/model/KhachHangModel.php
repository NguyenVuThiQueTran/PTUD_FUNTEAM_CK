<?php
// model/KhachHangModel.php

require_once dirname(__FILE__) . '/../config/database.php';

class KhachHangModel {
    
    private $conn;

    public function __construct() {
        $database = new Database();
        // === SỬA LỖI CÚ PHÁP TẠI ĐÂY ===
        $this->conn = $database->getConnection(); // Sửa $this. thành $this->
    }

    // Lấy danh sách
    public function getDanhSachKH() {
        try {
            $stmt = $this->conn->query("SELECT idKH AS MaKH, hoTen AS HoTen, email AS Email, soDienThoai AS DienThoai FROM khachhang ORDER BY hoTen");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ($result === false) ? array() : $result;
        } catch(PDOException $e) {
            error_log("KhachHangModel::getDanhSachKH error: " . $e->getMessage());
            return array();
        }
    }
    
    // MỚI: Hàm Thêm khách hàng (Sử dụng tên cột đúng)
    public function themKhachHang($hoTen, $email, $dienThoai) {
        try {
            // Sửa 'TenKH', 'SDT' thành 'HoTen', 'DienThoai'
            $stmt = $this->conn->prepare("INSERT INTO khachhang (hoTen, email, soDienThoai) VALUES (?, ?, ?)");
            if($stmt->execute(array($hoTen, $email, $dienThoai))) {
                // Trả về ID mới để controller có thể dùng
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            error_log('themKhachHang error: ' . $e->getMessage());
            return false;
        }
    }

    // MỚI: Hàm Sửa khách hàng (Sử dụng tên cột đúng)
    public function suaKhachHang($maKH, $hoTen, $email, $dienThoai) {
        try {
            // Sử dụng tên cột chính xác theo database: hoTen, email, soDienThoai, idKH
            $stmt = $this->conn->prepare("UPDATE khachhang SET hoTen=?, email=?, soDienThoai=? WHERE idKH=?");
            return $stmt->execute(array($hoTen, $email, $dienThoai, $maKH));
        } catch(PDOException $e) {
            error_log('suaKhachHang error: ' . $e->getMessage());
            return false;
        }
    }

    // MỚI: Hàm Xóa khách hàng (Sử dụng tên cột đúng)
    public function xoaKhachHang($maKH) {
        try {
            // Sử dụng idKH là khoá chính trong database
            $stmt = $this->conn->prepare("DELETE FROM khachhang WHERE idKH=?");
            return $stmt->execute(array($maKH));
        } catch(PDOException $e) {
            error_log('xoaKhachHang error: ' . $e->getMessage());
            return false;
        }
    }
}
?>