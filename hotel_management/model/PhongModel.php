<?php
// model/PhongModel.php

require_once dirname(__FILE__) . '/../config/database.php';

class PhongModel {
    
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy danh sách phòng (alias để view dùng các keys cũ)
    public function getDanhSachPhong() {
        $sql = "SELECT maPhong AS MaPhong, soPhong AS SoPhong, tangPhong AS Tang, hangPhong AS HangPhong, sucChua AS SucChua, giaPhong AS DonGia, tinhTrang AS TrangThai FROM phong ORDER BY CAST(tangPhong AS UNSIGNED), CAST(soPhong AS UNSIGNED)";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái (tinhTrang)
    public function capNhatTrangThai($maPhong, $trangThai) {
        $stmt = $this->conn->prepare("UPDATE phong SET tinhTrang=? WHERE maPhong=?");
        return $stmt->execute(array($trangThai, $maPhong));
    }

    // Thêm phòng - tạo mã MaPhong dạng P + số phòng và trả lại mã mới
    public function themPhong($soPhong, $tang, $hangPhong, $sucChua, $donGia) {
        try {
            // Tạo mã phòng theo yêu cầu: 'P' + số phòng
            $maPhong = 'P' . preg_replace('/[^0-9]/', '', $soPhong);

            // Kiểm tra trùng: mã phòng hoặc (số phòng + tầng)
            $check = $this->conn->prepare("SELECT COUNT(*) as c FROM phong WHERE maPhong = ? OR (soPhong = ? AND tangPhong = ?)");
            $check->execute(array($maPhong, $soPhong, $tang));
            $c = $check->fetch(PDO::FETCH_ASSOC);
            if($c && isset($c['c']) && (int)$c['c'] > 0) {
                return false; // duplicate
            }

            // Insert
            $stmt = $this->conn->prepare("INSERT INTO phong (maPhong, maLoaiPhong, tinhTrang, giaPhong, sucChua, tangPhong, soPhong, hangPhong) VALUES (?, NULL, 'Trống', ?, ?, ?, ?, ?)");
            if ($stmt->execute(array($maPhong, $donGia, $sucChua, $tang, $soPhong, $hangPhong))) {
                return $maPhong;
            }
            return false;
        } catch(PDOException $e) {
            error_log("PhongModel::themPhong() error: " . $e->getMessage());
            return false;
        }
    }

    // Sửa phòng
    public function suaPhong($maPhong, $soPhong, $tang, $hangPhong, $sucChua, $donGia) {
        try {
            $stmt = $this->conn->prepare("UPDATE phong SET soPhong=?, tangPhong=?, hangPhong=?, sucChua=?, giaPhong=? WHERE maPhong=?");
            return $stmt->execute(array($soPhong, $tang, $hangPhong, $sucChua, $donGia, $maPhong));
        } catch(PDOException $e) {
            return false;
        }
    }

    // Xóa phòng
    public function xoaPhong($maPhong) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM phong WHERE maPhong=?");
            return $stmt->execute(array($maPhong));
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>