<?php
session_start();

// --- PHẦN 1: XỬ LÝ THÊM NHANH TỪ DASHBOARD (AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'themNhanh') {
    require_once dirname(__FILE__) . '/../config/database.php';
    $db = new Database();
    $conn = $db->getConnection();

    $soPhong = trim($_POST['soPhong']);
    $maLoai = $_POST['hangPhong']; 
    $tang = $_POST['tangPhong'];
    $sucChua = !empty($_POST['sucChua']) ? $_POST['sucChua'] : 2;
    $giaInput = !empty($_POST['giaPhong']) ? $_POST['giaPhong'] : 0;

    // 1. Kiểm tra giá tiền
    if (!empty($_POST['giaPhong']) && (!is_numeric($giaInput) || $giaInput <= 0)) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Giá phòng phải là số dương lớn hơn 0!'));
        exit;
    }

    // 2. [MỚI] Kiểm tra trùng Số phòng
    $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM phong WHERE soPhong = ?");
    $stmtCheck->execute(array($soPhong));
    if ($stmtCheck->fetchColumn() > 0) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Số phòng ' . $soPhong . ' đã tồn tại!'));
        exit;
    }

    $gia = $giaInput;
    $maPhong = "P" . $soPhong; // Tự động tạo mã

    // Logic tên hạng phòng và giá mặc định
    $tenHang = "Standard";
    if ($maLoai == 'LP002') $tenHang = 'Superior';
    if ($maLoai == 'LP003') $tenHang = 'Deluxe';
    if ($maLoai == 'LP004') $tenHang = 'Suite';
    if ($maLoai == 'LP005') $tenHang = 'Family';

    if ($gia == 0) {
        if ($maLoai == 'LP001') $gia = 500000;
        elseif ($maLoai == 'LP002') $gia = 800000;
        elseif ($maLoai == 'LP003') $gia = 1200000;
        elseif ($maLoai == 'LP004') $gia = 2000000;
        elseif ($maLoai == 'LP005') $gia = 1500000;
    }

    try {
        $sql = "INSERT INTO phong (maPhong, maLoaiPhong, tinhTrang, giaPhong, sucChua, tangPhong, soPhong, hangPhong) 
                VALUES (?, ?, 'Trống', ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array($maPhong, $maLoai, $gia, $sucChua, $tang, $soPhong, $tenHang));
        echo json_encode(array('success' => true, 'message' => 'Thêm phòng thành công!'));
    } catch (Exception $e) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Không thể thêm phòng (Lỗi hệ thống)'));
    }
    exit; 
}

// --- PHẦN 2: XỬ LÝ QUẢN LÝ PHÒNG (TRANG QUẢN LÝ) ---

if(!isset($_SESSION['user']) && !isset($_POST['action'])) {
    exit;
}

require_once dirname(__FILE__) . '/../model/PhongModel.php';
// Cần kết nối DB ở đây để kiểm tra trùng lặp thủ công nếu Model chưa hỗ trợ
require_once dirname(__FILE__) . '/../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$phongModel = new PhongModel(); 

if(isset($_POST['action'])){
    $action = $_POST['action'];

    // Cập nhật trạng thái
    if($action == 'capNhatTrangThai') {
        $maPhong = $_POST['maPhong'];
        $trangThai = $_POST['trangThai'];
        if($phongModel->capNhatTrangThai($maPhong, $trangThai)) {
            echo json_encode(array('success'=>true,'message'=>'Cập nhật trạng thái thành công'));
        } else {
            echo json_encode(array('success'=>false,'message'=>'Cập nhật thất bại'));
        }
        exit;
    }

    // Thêm phòng (Từ trang quản lý)
    if($action == 'themPhong') {
        $soPhong = isset($_POST['soPhong']) ? trim($_POST['soPhong']) : '';
        $tang = isset($_POST['tang']) ? trim($_POST['tang']) : '';
        $hangPhong = isset($_POST['hangPhong']) ? trim($_POST['hangPhong']) : '';
        $sucChua = isset($_POST['sucChua']) ? (int)$_POST['sucChua'] : 0;
        $donGia = isset($_POST['donGia']) ? trim($_POST['donGia']) : 0;

        if($soPhong === '') { echo json_encode(array('success'=>false,'message'=>'Số phòng rỗng')); exit; }
        
        // RÀNG BUỘC 1: Giá > 0
        if(!is_numeric($donGia) || $donGia <= 0){
            echo json_encode(array('success'=>false,'message'=>'Giá phòng phải lớn hơn 0')); exit;
        }

        // RÀNG BUỘC 2: [MỚI] Kiểm tra trùng Số phòng
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM phong WHERE soPhong = ?");
        $stmtCheck->execute(array($soPhong));
        if ($stmtCheck->fetchColumn() > 0) {
            echo json_encode(array('success' => false, 'message' => 'Lỗi: Số phòng ' . $soPhong . ' đã tồn tại!'));
            exit;
        }
        
        $result = $phongModel->themPhong($soPhong, $tang, $hangPhong, $sucChua, $donGia);
        if($result) {
            echo json_encode(array('success'=>true,'message'=>'Thêm phòng thành công'));
        } else {
            echo json_encode(array('success'=>false,'message'=>'Thêm thất bại (Lỗi hệ thống)'));
        }
        exit;
    }

    // Sửa phòng
    if($action == 'suaPhong') {
        $maPhong = $_POST['maPhong']; // Mã phòng cũ (ID)
        $soPhong = trim($_POST['soPhong']); // Số phòng mới (có thể người dùng sửa số)
        $tang = $_POST['tang'];
        $hangPhong = $_POST['hangPhong'];
        $sucChua = (int)$_POST['sucChua'];
        $donGia = $_POST['donGia'];
        
        // RÀNG BUỘC 1: Giá > 0
        if(!is_numeric($donGia) || $donGia <= 0){
            echo json_encode(array('success'=>false,'message'=>'Giá phòng phải lớn hơn 0')); exit;
        }

        // RÀNG BUỘC 2: [MỚI] Kiểm tra trùng Số phòng (Trừ chính nó ra)
        // Logic: Tìm xem có phòng nào KHÁC có cùng số phòng này không
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM phong WHERE soPhong = ? AND maPhong != ?");
        $stmtCheck->execute(array($soPhong, $maPhong));
        if ($stmtCheck->fetchColumn() > 0) {
            echo json_encode(array('success' => false, 'message' => 'Lỗi: Số phòng ' . $soPhong . ' đã được sử dụng bởi phòng khác!'));
            exit;
        }

        if($phongModel->suaPhong($maPhong, $soPhong, $tang, $hangPhong, $sucChua, $donGia)) {
            echo json_encode(array('success'=>true,'message'=>'Cập nhật thành công'));
        } else {
            echo json_encode(array('success'=>false,'message'=>'Cập nhật thất bại'));
        }
        exit;
    }
    
    // Xóa phòng
    if($action == 'xoaPhong') {
        if($phongModel->xoaPhong($_POST['maPhong'])) {
            echo json_encode(array('success'=>true,'message'=>'Xóa thành công'));
        } else {
            echo json_encode(array('success'=>false,'message'=>'Xóa thất bại'));
        }
        exit;
    }
}

// --- 3. HIỂN THỊ DANH SÁCH ---
$dsPhong = $phongModel->getDanhSachPhong();
$hangPhongList = array('Standard','Superior','Deluxe','Suite','Family');
$tangList = range(1,9);

include dirname(__FILE__) . '/../view/quanlyphong.php';
?>