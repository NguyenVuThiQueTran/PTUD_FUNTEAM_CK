<?php
session_start();
require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../model/KhuyenMaiModel.php';

$kmModel = new KhuyenMaiModel();
$action = isset($_POST['action']) ? $_POST['action'] : '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    header('Content-Type: application/json; charset=utf-8');
}

// --- THÊM KHUYẾN MÃI ---
if($action == 'themKM') {
    $maKM = trim($_POST['maKM']);
    $tenCT = trim($_POST['tenCT']);
    $mucGiam = $_POST['mucGiam'];
    $ngayBd = $_POST['ngayBatDau'];
    $ngayKt = $_POST['ngayKetThuc'];

    if($kmModel->checkMaKM($maKM)) {
        echo json_encode(array('success'=>false, 'message'=>'Mã khuyến mãi đã tồn tại!'));
        exit;
    }
    if(strtotime($ngayBd) > strtotime($ngayKt)) {
        echo json_encode(array('success'=>false, 'message'=>'Ngày bắt đầu phải nhỏ hơn ngày kết thúc!'));
        exit;
    }

    if($kmModel->themKhuyenMai($maKM, $tenCT, $mucGiam, $ngayBd, $ngayKt)) {
        echo json_encode(array('success'=>true, 'message'=>'Thêm thành công!'));
    } else {
        echo json_encode(array('success'=>false, 'message'=>'Thêm thất bại!'));
    }
    exit;
}

// --- SỬA KHUYẾN MÃI ---
elseif($action == 'suaKM') {
    $maKM = $_POST['maKM'];
    $tenCT = trim($_POST['tenCT']);
    $mucGiam = $_POST['mucGiam'];
    $ngayBd = $_POST['ngayBatDau'];
    $ngayKt = $_POST['ngayKetThuc'];

    if(strtotime($ngayBd) > strtotime($ngayKt)) {
        echo json_encode(array('success'=>false, 'message'=>'Ngày kết thúc không hợp lệ!'));
        exit;
    }

    if($kmModel->suaKhuyenMai($maKM, $tenCT, $mucGiam, $ngayBd, $ngayKt)) {
        echo json_encode(array('success'=>true, 'message'=>'Cập nhật thành công!'));
    } else {
        echo json_encode(array('success'=>false, 'message'=>'Cập nhật thất bại!'));
    }
    exit;
}

// --- XÓA KHUYẾN MÃI ---
elseif($action == 'xoaKM') {
    $maKM = $_POST['maKM'];
    if($kmModel->xoaKhuyenMai($maKM)) {
        echo json_encode(array('success'=>true, 'message'=>'Xóa thành công!'));
    } else {
        echo json_encode(array('success'=>false, 'message'=>'Xóa thất bại!'));
    }
    exit;
}

// --- MẶC ĐỊNH: LOAD DANH SÁCH ---
else {
    $dsKhuyenMai = $kmModel->getDanhSachKM();
    if(!is_array($dsKhuyenMai)) $dsKhuyenMai = array();
    include dirname(__FILE__) . '/../view/quanlykhuyenmai.php';
}
?>