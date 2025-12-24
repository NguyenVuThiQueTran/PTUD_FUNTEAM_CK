<?php
require_once("../model/clsUser.php");

class cUser {
    private $model;

    public function __construct() {
        $this->model = new clsUser();
    }

    // Đăng ký khách hàng
    public function cRegister($email, $matKhau, $hoTen, $CCCD) {
        $ketQua = $this->model->themKhachHangVaTaiKhoan($hoTen, $email, $matKhau, null, $CCCD);
        return $ketQua;
    }

    // Đăng nhập
    public function cLogin($email, $matKhau) {
        $ketQua = $this->model->kiemTraDangNhap($email, $matKhau);
        return $ketQua;
    }
}
?>