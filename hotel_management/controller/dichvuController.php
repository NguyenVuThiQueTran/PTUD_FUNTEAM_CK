<?php
session_start();

// --- PHẦN 1: XỬ LÝ THÊM NHANH TỪ DASHBOARD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'themNhanh') {
    require_once dirname(__FILE__) . '/../config/database.php';
    $db = new Database();
    $conn = $db->getConnection();

    $maDV = "DV".rand(100,999);
    $loaiDV = isset($_POST['loaiDV']) ? $_POST['loaiDV'] : 'Tiện ích';
    
    try {
        $sql = "INSERT INTO dichvu (maDV, tenDV, loaiDV, donGia, moTa, trangThai) VALUES (?, ?, ?, ?, ?, 'HoatDong')";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array($maDV, $_POST['tenDV'], $loaiDV, $_POST['donGia'], $_POST['moTa']));
        echo json_encode(array('success'=>true, 'message'=>'Thêm dịch vụ thành công!'));
    } catch (Exception $e) {
        echo json_encode(array('success'=>false, 'message'=>'Lỗi SQL'));
    }
    exit;
}

// --- PHẦN 2: QUẢN LÝ DỊCH VỤ ---
if(!isset($_SESSION['user']) && !isset($_POST['action'])) exit;

require_once dirname(__FILE__) . '/../model/DichVuModel.php';
$dichVuModel = new DichVuModel();

if(isset($_POST['action'])){
    $action = $_POST['action'];

    if($action == 'themDichVu') {
        $tenDV = trim($_POST['tenDV']);
        $donGia = trim($_POST['donGia']);
        $moTa = trim($_POST['moTa']);

        if($dichVuModel->existsByTenDV($tenDV)){
            echo json_encode(array('success'=>false,'message'=>'Tên dịch vụ đã tồn tại')); exit;
        }

        $newId = $dichVuModel->themDichVu($tenDV, $donGia, $moTa);
        if($newId) {
            echo json_encode(array('success'=>true, 'message'=>'Thêm thành công'));
        } else {
            echo json_encode(array('success'=>false, 'message'=>'Thêm thất bại'));
        }
        exit;
    }
    
    if($action == 'suaDichVu') {
        if($dichVuModel->suaDichVu($_POST['maDV'], $_POST['tenDV'], $_POST['donGia'], $_POST['moTa'])) {
            echo json_encode(array('success'=>true, 'message'=>'Cập nhật thành công'));
        } else {
            echo json_encode(array('success'=>false, 'message'=>'Cập nhật thất bại'));
        }
        exit;
    }

    if($action == 'xoaDichVu') {
        if($dichVuModel->xoaDichVu($_POST['maDV'])) {
            echo json_encode(array('success'=>true, 'message'=>'Xóa thành công'));
        } else {
            echo json_encode(array('success'=>false, 'message'=>'Xóa thất bại'));
        }
        exit;
    }
}

$dsDichVu = $dichVuModel->getDanhSachDV(); 
include dirname(__FILE__) . '/../view/quanlydichvu.php';
?>