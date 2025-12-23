<?php
require_once("../model/clsDoan.php");

class cDoan {
    private $model;

    public function __construct() {
        $this->model = new clsDoan();
    }

    // Thêm đoàn mới
    public function themDoan($maDoan, $tenDoan, $truongDoan, $soLuong, $soDienThoai) {
        // Tạo email và mật khẩu mặc định
        $emailDoan = $this->generateEmail($tenDoan);
        $matKhau = $this->generatePassword();
        
        // Thêm đoàn vào database
        $ketQua = $this->model->themDoan($maDoan, $tenDoan, $emailDoan, $matKhau, $soLuong, $soDienThoai);
        
        if (!$ketQua) {
            return false;
        }
        
        // Tạo tài khoản cho đoàn
        $tkKetQua = $this->model->taoTaiKhoanDoan($emailDoan, $matKhau, $maDoan);
        
        if (!$tkKetQua) {
            // Xóa đoàn đã tạo nếu lỗi tài khoản
            $this->model->xoaDoan($maDoan);
            return false;
        }
        
        return array(
            'maDoan' => $maDoan,
            'email' => $emailDoan,
            'matKhau' => $matKhau
        );
    }

    // Thêm thành viên vào đoàn đã có
    public function themThanhVienDoan($maDoan, $idKH) {
        $ketQua = $this->model->themThanhVien($maDoan, $idKH, 'thanhvien');
        return $ketQua;
    }

    // Tạo email tự động
    private function generateEmail($tenDoan) {
        $sanitizedName = preg_replace('/[^a-zA-Z0-9]/', '', $tenDoan);
        $sanitizedName = strtolower(substr($sanitizedName, 0, 10));
        return $sanitizedName . rand(100, 999) . '@doan.com';
    }

    // Tạo mật khẩu tự động
    private function generatePassword() {
        return 'doan' . rand(1000, 9999);
    }
}
?>