<?php
// model/NhanVienModel.php

require_once dirname(__FILE__) . '/../config/database.php';

class NhanVienModel {
    
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy danh sách nhân sự (join với taikhoan để lấy email và vaiTro)
    public function getDanhSachNV() {
        $sql = "SELECT n.maNS AS MaNV, n.hoTen AS HoTen, t.vaiTro AS ChucVu, n.soDienThoai AS DienThoai, t.email AS Email
                FROM nhansu n
                LEFT JOIN taikhoan t ON t.idUser = n.idUser
                ORDER BY n.hoTen";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy danh sách chức vụ (vaiTro trong taikhoan)
    public function getDanhSachChucVu() {
        try {
            $stmt = $this->conn->query("SELECT DISTINCT vaiTro AS ChucVu FROM taikhoan WHERE loaiTaiKhoan='NhanVien' ORDER BY vaiTro");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return array();
        }
    }

    // Thêm nhân viên: tạo taikhoan + nhansu (trong transaction)
    public function themNhanVien($hoTen, $chucVu, $dienThoai, $email, $matKhau) {
        try {
            $this->conn->beginTransaction();

            // 1) Insert vào taikhoan (matKhau lưu MD5 cho tương thích với dữ liệu hiện có)
            $stmt = $this->conn->prepare("INSERT INTO taikhoan (email, matKhau, loaiTaiKhoan, idThamChieu, vaiTro, trangThai, ngayTao) VALUES (?, ?, 'NhanVien', NULL, ?, 'HoatDong', NOW())");
            $stmt->execute(array($email, md5($matKhau), $chucVu));
            $idUser = $this->conn->lastInsertId();

            // 2) Tạo mã nhân sự mới (NSxxx)
            $stmt = $this->conn->query("SELECT maNS FROM nhansu ORDER BY maNS DESC LIMIT 1");
            $last = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextNum = 1;
            if ($last && isset($last['maNS'])) {
                if (preg_match('/(\d+)$/', $last['maNS'], $m)) $nextNum = intval($m[1]) + 1;
            }
            $maNS = 'NS' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

            // 3) Insert vào nhansu
            $stmt = $this->conn->prepare("INSERT INTO nhansu (maNS, idUser, hoTen, gioiTinh, soDienThoai, ngayVaoLam) VALUES (?, ?, ?, NULL, ?, CURDATE())");
            $stmt->execute(array($maNS, $idUser, $hoTen, $dienThoai));

            $this->conn->commit();
            return $maNS;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            error_log('themNhanVien error: ' . $e->getMessage());
            return false;
        }
    }

    // Sửa nhân viên: cập nhật nhansu + taikhoan
    public function suaNhanVien($maNV, $hoTen, $chucVu, $dienThoai, $email) {
        try {
            $this->conn->beginTransaction();

            // Lấy idUser
            $stmt = $this->conn->prepare("SELECT idUser FROM nhansu WHERE maNS = ? LIMIT 1");
            $stmt->execute(array($maNV));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$row) { $this->conn->rollBack(); return false; }
            $idUser = $row['idUser'];

            // Cập nhật nhansu
            $stmt = $this->conn->prepare("UPDATE nhansu SET hoTen=?, soDienThoai=? WHERE maNS=?");
            $stmt->execute(array($hoTen, $dienThoai, $maNV));

            // Cập nhật taikhoan
            if($idUser) {
                $stmt = $this->conn->prepare("UPDATE taikhoan SET email=?, vaiTro=? WHERE idUser=?");
                $stmt->execute(array($email, $chucVu, $idUser));
            }

            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            error_log('suaNhanVien error: ' . $e->getMessage());
            return false;
        }
    }

    // Xóa nhân viên: xóa nhansu và taikhoan liên quan
    public function xoaNhanVien($maNV) {
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("SELECT idUser FROM nhansu WHERE maNS = ? LIMIT 1");
            $stmt->execute(array($maNV));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $idUser = $row ? $row['idUser'] : null;

            // Xóa nhansu
            $stmt = $this->conn->prepare("DELETE FROM nhansu WHERE maNS = ?");
            $stmt->execute(array($maNV));

            // Xóa taikhoan nếu có và nếu nó là loại NhanVien
            if($idUser) {
                $stmt = $this->conn->prepare("DELETE FROM taikhoan WHERE idUser = ? AND loaiTaiKhoan = 'NhanVien'");
                $stmt->execute(array($idUser));
            }

            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            error_log('xoaNhanVien error: ' . $e->getMessage());
            return false;
        }
    }

    // Kiểm tra tồn tại theo Email hoặc SĐT (taikhoan.email / nhansu.soDienThoai)
    public function existsByEmailOrPhone($email, $phone) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as c FROM taikhoan t LEFT JOIN nhansu n ON t.idUser = n.idUser WHERE t.email = ? OR n.soDienThoai = ?");
            $stmt->execute(array($email, $phone));
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($r && isset($r['c']) && (int)$r['c']>0);
        } catch(PDOException $e) {
            error_log('existsByEmailOrPhone error: ' . $e->getMessage());
            return false;
        }
    }
}
?>