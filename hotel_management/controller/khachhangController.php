<?php
session_start();

// --- PHẦN 1: XỬ LÝ THÊM NHANH TỪ DASHBOARD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'themNhanh') {
    require_once dirname(__FILE__) . '/../config/database.php';
    $db = new Database();
    $conn = $db->getConnection();

    $hoTen = $_POST['hoTen'];
    $email = $_POST['email'];
    $sdt = $_POST['soDienThoai'];
    
    // --- RÀNG BUỘC SĐT 10 SỐ ---
    if (!preg_match('/^[0-9]{10}$/', $sdt)) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Số điện thoại phải đúng 10 chữ số!'));
        exit;
    }

    $cccd = isset($_POST['cccd']) ? $_POST['cccd'] : '';
    $diaChi = isset($_POST['diaChi']) ? $_POST['diaChi'] : '';
    
    // Mật khẩu mặc định 123456
    $matKhau = md5('123456');

    try {
        $sql = "INSERT INTO khachhang (hoTen, email, matKhau, soDienThoai, CCCD, diaChi, loaiKH, vaiTro, trangThai) 
                VALUES (?, ?, ?, ?, ?, ?, 'Thuong', 'KhachHang', 'HoatDong')";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array($hoTen, $email, $matKhau, $sdt, $cccd, $diaChi));
        echo json_encode(array('success'=>true, 'message'=>'Thêm khách hàng thành công!'));
    } catch (Exception $e) {
        echo json_encode(array('success'=>false, 'message'=>'Lỗi: Email hoặc SĐT đã tồn tại'));
    }
    exit;
}

// --- PHẦN 2: QUẢN LÝ KHÁCH HÀNG (TRANG QUẢN LÝ) ---
if(!isset($_SESSION['user']) && !isset($_POST['action'])) exit;

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../model/KhachHangModel.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    header('Content-Type: application/json; charset=utf-8');
}

if($action=='themKhachHang'){
    $hoTen = trim($_POST['hoTen']);
    $email = trim($_POST['email']);
    $dienThoai = trim($_POST['dienThoai']);

    // --- RÀNG BUỘC SĐT 10 SỐ ---
    if (!preg_match('/^[0-9]{10}$/', $dienThoai)) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Số điện thoại phải đúng 10 chữ số!'));
        exit;
    }

    $model = new KhachHangModel();
    $newId = $model->themKhachHang($hoTen, $email, $dienThoai);

    if($newId) {
        echo json_encode(array('success'=>true,'message'=>"Thêm thành công"));
    } else {
        echo json_encode(array('success'=>false,'message'=>'Thêm thất bại (Email/SĐT trùng)'));
    }
    exit;
}
elseif($action=='suaKhachHang'){
    $model = new KhachHangModel();
    // Khi sửa cũng nên check nhưng tạm thời giữ nguyên logic cũ
    if($model->suaKhachHang($_POST['maKH'], $_POST['hoTen'], $_POST['email'], $_POST['dienThoai'])){
        echo json_encode(array('success'=>true,'message'=>"Cập nhật thành công"));
    } else {
        echo json_encode(array('success'=>false,'message'=>'Cập nhật thất bại'));
    }
    exit;
}
elseif($action=='xoaKhachHang'){
    $model = new KhachHangModel();
    if($model->xoaKhachHang($_POST['maKH'])){
        echo json_encode(array('success'=>true,'message'=>'Xóa thành công'));
    } else {
        echo json_encode(array('success'=>false,'message'=>'Xóa thất bại'));
    }
    exit;
}
else {
    $model = new KhachHangModel();
    $dsKhachHang = $model->getDanhSachKH();
    if(!is_array($dsKhachHang)) $dsKhachHang = array();
    include dirname(__FILE__) . '/../view/quanlykhachhang.php';
}
?>