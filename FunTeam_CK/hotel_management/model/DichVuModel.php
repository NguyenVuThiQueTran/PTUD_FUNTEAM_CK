<?php
// model/DichVuModel.php

require_once dirname(__FILE__) . '/../config/database.php';

class DichVuModel {
    
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy danh sách (alias các cột để view dùng các keys cũ)
    public function getDanhSachDV() {
        $sql = "SELECT maDV AS MaDV, tenDV AS TenDV, loaiDV AS LoaiDV, donGia AS DonGia, trangThai AS TrangThai, moTa AS MoTa FROM dichvu ORDER BY tenDV";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Hàm tạo mã MaDV dạng DV###
    protected function createMaDV(){
        $stmt = $this->conn->query("SELECT maDV FROM dichvu ORDER BY maDV DESC LIMIT 1");
        $last = $stmt->fetch(PDO::FETCH_ASSOC);
        $next = 1;
        if($last && isset($last['maDV'])){
            if(preg_match('/(\d+)$/', $last['maDV'], $m)) $next = intval($m[1]) + 1;
        }
        return 'DV' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    // MỚI: Hàm Thêm dịch vụ (Đã sửa DonGia, MoTa)
    public function themDichVu($tenDV, $donGia, $moTa) {
        try {
            $maDV = $this->createMaDV();
            $stmt = $this->conn->prepare("INSERT INTO dichvu (maDV, tenDV, loaiDV, donGia, trangThai, moTa) VALUES (?, ?, NULL, ?, 'HoatDong', ?)");
            if ($stmt->execute(array($maDV, $tenDV, $donGia, $moTa))) {
                return $maDV;
            }
            return false;
        } catch(PDOException $e) {
            error_log('themDichVu error: '.$e->getMessage());
            return false;
        }
    }

    // MỚI: Hàm Sửa dịch vụ (Đã sửa DonGia, MoTa)
    public function suaDichVu($maDV, $tenDV, $donGia, $moTa) {
        try {
            $stmt = $this->conn->prepare("UPDATE dichvu SET tenDV=?, donGia=?, moTa=? WHERE maDV=?");
            return $stmt->execute(array($tenDV, $donGia, $moTa, $maDV));
        } catch(PDOException $e) {
            return false;
        }
    }

    // Kiểm tra tồn tại dịch vụ theo tên
    public function existsByTenDV($tenDV) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as c FROM dichvu WHERE tenDV = ?");
            $stmt->execute(array($tenDV));
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($r && isset($r['c']) && (int)$r['c']>0);
        } catch(PDOException $e) {
            return false;
        }
    }

    // MỚI: Hàm Xóa dịch vụ
    public function xoaDichVu($maDV) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM dichvu WHERE maDV=?");
            return $stmt->execute(array($maDV));
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>