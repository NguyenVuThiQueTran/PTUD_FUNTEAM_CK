<?php
session_start();

// --- PHẦN 1: XỬ LÝ THÊM NHANH TỪ DASHBOARD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'themNhanh') {
    require_once dirname(__FILE__) . '/../config/database.php';
    $db = new Database();
    $conn = $db->getConnection();

    $hoTen = $_POST['hoTen'];
    $email = $_POST['email'];
    $gioiTinh = isset($_POST['gioiTinh']) ? $_POST['gioiTinh'] : 'Nam';
    $sdt = $_POST['soDienThoai'];
    
    // --- RÀNG BUỘC SĐT 10 SỐ ---
    if (!preg_match('/^[0-9]{10}$/', $sdt)) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi: Số điện thoại phải đúng 10 chữ số!'));
        exit;
    }

    $ngay = isset($_POST['ngayVaoLam']) ? $_POST['ngayVaoLam'] : date('Y-m-d');
    $vaiTro = isset($_POST['vaiTro']) ? $_POST['vaiTro'] : 'NhanVien';
    $matKhauRaw = isset($_POST['matKhau']) ? $_POST['matKhau'] : '123456';
    $matKhauEnc = md5($matKhauRaw);

    try {
        $conn->beginTransaction();

        // B1: Tạo tài khoản
        $stmtTK = $conn->prepare("INSERT INTO taikhoan (email, matKhau, loaiTaiKhoan, vaiTro, trangThai, ngayTao) VALUES (?, ?, 'NhanVien', ?, 'HoatDong', NOW())");
        $stmtTK->execute(array($email, $matKhauEnc, $vaiTro));
        $idUser = $conn->lastInsertId();

        // B2: Tạo nhân viên
        $maNS = "NS" . rand(1000,9999);
        $stmtNS = $conn->prepare("INSERT INTO nhansu (maNS, idUser, hoTen, gioiTinh, soDienThoai, ngayVaoLam) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtNS->execute(array($maNS, $idUser, $hoTen, $gioiTinh, $sdt, $ngay));

        $conn->commit();
        echo json_encode(array('success'=>true, 'message'=>'Thêm nhân viên thành công!'));
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(array('success'=>false, 'message'=>'Lỗi: Email đã tồn tại hoặc lỗi hệ thống'));
    }
    exit;
}

// --- PHẦN 2: QUẢN LÝ NHÂN VIÊN (TRANG QUẢN LÝ) ---
if(!isset($_SESSION['user']) && !isset($_POST['action'])) exit;

require_once dirname(__FILE__) . '/../model/NhanVienModel.php';
$nhanVienModel = new NhanVienModel();

if(isset($_POST['action'])){
    $action = $_POST['action'];

    if($action == 'themNhanVien') {
        $hoTen = $_POST['hoTen'];
        $chucVu = $_POST['chucVu'];
        $dienThoai = $_POST['dienThoai'];
        $email = $_POST['email'];
        $matKhau = $_POST['matKhau'];

        // --- RÀNG BUỘC SĐT 10 SỐ ---
        if (!preg_match('/^[0-9]{10}$/', $dienThoai)) {
            echo json_encode(array('success' => false, 'message' => 'Lỗi: Số điện thoại phải đúng 10 chữ số!'));
            exit;
        }

        if($nhanVienModel->existsByEmailOrPhone($email, $dienThoai)){
            echo json_encode(array('success'=>false, 'message'=>'Email hoặc SĐT đã tồn tại'));
            exit;
        }

        $newId = $nhanVienModel->themNhanVien($hoTen, $chucVu, $dienThoai, $email, $matKhau);
        if($newId) {
            echo json_encode(array('success'=>true, 'message'=>'Thêm thành công'));
        } else {
            echo json_encode(array('success'=>false, 'message'=>'Thêm thất bại'));
        }
        exit;
    }
    
    if($action == 'suaNhanVien') {
        if($nhanVienModel->suaNhanVien($_POST['maNV'], $_POST['hoTen'], $_POST['chucVu'], $_POST['dienThoai'], $_POST['email'])) {
            echo json_encode(array('success'=>true, 'message'=>'Cập nhật thành công'));
        } else {
            echo json_encode(array('success'=>false, 'message'=>'Cập nhật thất bại'));
        }
        exit;
    }

    if($action == 'xoaNhanVien') {
        if($nhanVienModel->xoaNhanVien($_POST['maNV'])) {
            echo json_encode(array('success'=>true, 'message'=>'Xóa thành công'));
        } else {
            echo json_encode(array('success'=>false, 'message'=>'Xóa thất bại'));
        }
        exit;
    }
}

$dsNhanVien = $nhanVienModel->getDanhSachNV(); 
$dsChucVu = $nhanVienModel->getDanhSachChucVu(); 
include dirname(__FILE__) . '/../view/quanlynhanvien.php';
?>