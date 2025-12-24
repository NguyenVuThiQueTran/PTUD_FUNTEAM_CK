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
    // Lấy CCCD từ form (nếu không có thì để rỗng)
    $cccd = isset($_POST['cccd']) ? trim($_POST['cccd']) : ''; 
    
    // 1. Ràng buộc SĐT 10 số
    if (!preg_match('/^[0-9]{10}$/', $sdt)) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Số điện thoại phải đúng 10 chữ số!'));
        exit;
    }

    // 2. Check trùng CCCD (nếu có nhập)
    if($cccd != ''){
        $chk = $conn->prepare("SELECT COUNT(*) FROM khachhang WHERE CCCD = ?");
        $chk->execute(array($cccd));
        if($chk->fetchColumn() > 0){
            echo json_encode(array('success' => false, 'message' => 'Lỗi: Số CCCD đã tồn tại!'));
            exit;
        }
    }

    $matKhau = md5('123456');

    try {
        $sql = "INSERT INTO khachhang (hoTen, email, matKhau, soDienThoai, CCCD, diaChi, loaiKH, vaiTro, trangThai) 
                VALUES (?, ?, ?, ?, ?, '', 'Thuong', 'KhachHang', 'HoatDong')";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array($hoTen, $email, $matKhau, $sdt, $cccd));
        echo json_encode(array('success'=>true, 'message'=>'Thêm khách hàng thành công!'));
    } catch (Exception $e) {
        echo json_encode(array('success'=>false, 'message'=>'Lỗi: Email hoặc SĐT đã tồn tại'));
    }
    exit;
}

// ... (Phần dưới giữ nguyên như cũ) ...
// === NẾU PHẦN DƯỚI BẠN CHƯA CÓ CODE, HÃY COPY TOÀN BỘ FILE CONTROLLER CŨ VÀO ĐÂY SAU DÒNG NÀY ===
// (Để an toàn, nếu bạn đã có file controller hoàn chỉnh, chỉ cần thay thế phần IF đầu tiên này thôi)
// Tuy nhiên, tôi sẽ dán nốt phần còn lại cho chắc chắn đồng bộ:

// --- PHẦN 2: QUẢN LÝ KHÁCH HÀNG (TRANG QUẢN LÝ CHÍNH) ---
if(!isset($_SESSION['user']) && !isset($_POST['action'])) exit;

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../model/KhachHangModel.php';

$db = new Database();
$conn = $db->getConnection();

$action = isset($_POST['action']) ? $_POST['action'] : '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    header('Content-Type: application/json; charset=utf-8');
}

// === XỬ LÝ THÊM KHÁCH HÀNG ===
if($action=='themKhachHang'){
    $hoTen = trim($_POST['hoTen']);
    $email = trim($_POST['email']);
    $dienThoai = trim($_POST['dienThoai']);
    $cccd = trim($_POST['cccd']); 

    if (!preg_match('/^[0-9]{10}$/', $dienThoai)) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Số điện thoại phải đúng 10 chữ số!'));
        exit;
    }

    $chkCCCD = $conn->prepare("SELECT COUNT(*) FROM khachhang WHERE CCCD = ?");
    $chkCCCD->execute(array($cccd));
    if($chkCCCD->fetchColumn() > 0){
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Số CCCD này đã tồn tại trong hệ thống!'));
        exit;
    }

    try {
        $sql = "INSERT INTO khachhang (hoTen, email, soDienThoai, CCCD, loaiKH, trangThai, matKhau) VALUES (?, ?, ?, ?, 'Thuong', 'HoatDong', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array($hoTen, $email, $dienThoai, $cccd, md5('123456')));
        
        $newId = $conn->lastInsertId();
        echo json_encode(array(
            'success'=>true, 
            'message'=>"Thêm thành công", 
            'data'=>array('MaKH'=>$newId, 'HoTen'=>$hoTen, 'Email'=>$email, 'DienThoai'=>$dienThoai, 'CCCD'=>$cccd)
        ));
    } catch (Exception $e) {
        echo json_encode(array('success'=>false,'message'=>'Lỗi: Email hoặc SĐT đã tồn tại'));
    }
    exit;
}

elseif($action=='suaKhachHang'){
    $maKH = $_POST['maKH'];
    $hoTen = trim($_POST['hoTen']);
    $email = trim($_POST['email']);
    $dienThoai = trim($_POST['dienThoai']);
    $cccd = trim($_POST['cccd']);

    if (!preg_match('/^[0-9]{10}$/', $dienThoai)) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Số điện thoại phải đúng 10 chữ số!'));
        exit;
    }

    $chkCCCD = $conn->prepare("SELECT COUNT(*) FROM khachhang WHERE CCCD = ? AND idKH != ?");
    $chkCCCD->execute(array($cccd, $maKH));
    if($chkCCCD->fetchColumn() > 0){
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Số CCCD này đã thuộc về khách hàng khác!'));
        exit;
    }

    try {
        $sql = "UPDATE khachhang SET hoTen=?, email=?, soDienThoai=?, CCCD=? WHERE idKH=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array($hoTen, $email, $dienThoai, $cccd, $maKH));
        
        echo json_encode(array(
            'success'=>true,
            'message'=>"Cập nhật thành công", 
            'data'=>array('MaKH'=>$maKH, 'HoTen'=>$hoTen, 'Email'=>$email, 'DienThoai'=>$dienThoai, 'CCCD'=>$cccd)
        ));
    } catch (Exception $e) {
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