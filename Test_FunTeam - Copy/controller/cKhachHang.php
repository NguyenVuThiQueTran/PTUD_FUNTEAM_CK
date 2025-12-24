<?php
require_once("../model/clsKhachHang.php");

class cKhachHang {
    private $model;

    public function __construct() {
        $this->model = new clsKhachHang();
    }

    // Đăng ký khách hàng
    public function dangKyKhachHang($hoTen, $email, $soDienThoai, $CCCD, $diaChi, $matKhau, $xacNhanMatKhau) {
        // Validate dữ liệu đầu vào
        $errors = $this->validateDangKy($hoTen, $email, $soDienThoai, $CCCD, $matKhau, $xacNhanMatKhau);
        
        if (!empty($errors)) {
            return array(
                'success' => false,
                'message' => implode('<br>', $errors)
            );
        }
        
        // Gọi model đăng ký
        return $this->model->dangKyKhachHang($hoTen, $email, $soDienThoai, $CCCD, $diaChi, $matKhau);
    }

    // Validate dữ liệu đăng ký
    private function validateDangKy($hoTen, $email, $soDienThoai, $CCCD, $matKhau, $xacNhanMatKhau) {
        $errors = array();
        
        if (empty($hoTen)) {
            $errors[] = "Vui lòng nhập họ tên!";
        } elseif (strlen($hoTen) < 2) {
            $errors[] = "Họ tên phải có ít nhất 2 ký tự!";
        }
        
        if (empty($email)) {
            $errors[] = "Vui lòng nhập email!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không đúng định dạng!";
        }
        
        if (empty($soDienThoai)) {
            $errors[] = "Vui lòng nhập số điện thoại!";
        } elseif (!preg_match('/^[0-9]{10,11}$/', $soDienThoai)) {
            $errors[] = "Số điện thoại không hợp lệ (10-11 số)!";
        }
        
        if (empty($CCCD)) {
            $errors[] = "Vui lòng nhập số CCCD!";
        } elseif (!preg_match('/^[0-9]{9,12}$/', $CCCD)) {
            $errors[] = "Số CCCD không hợp lệ (9-12 số)!";
        }
        
        if (empty($matKhau)) {
            $errors[] = "Vui lòng nhập mật khẩu!";
        } elseif (strlen($matKhau) < 4) {
            $errors[] = "Mật khẩu phải có ít nhất 4 ký tự!";
        }
        
        if ($matKhau !== $xacNhanMatKhau) {
            $errors[] = "Mật khẩu xác nhận không khớp!";
        }
        
        return $errors;
    }

    // Đăng nhập
    public function dangNhap($email, $matKhau) {
        if (empty($email) || empty($matKhau)) {
            return array('success' => false, 'error' => 'Vui lòng nhập đầy đủ thông tin!');
        }
        
        return $this->model->dangNhap($email, $matKhau);
    }

    // Kiểm tra email tồn tại
    public function kiemTraEmail($email) {
        return $this->model->kiemTraEmailTonTai($email);
    }
}
?>