<?php
// CRITICAL: Không xuất bất kỳ output nào trước JSON response
ob_start(); // Bắt đầu output buffering

// Set encoding
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Tắt hiển thị lỗi để không làm hỏng JSON

// Include model
require_once("../model/clsDichVu.php");

// Khởi tạo model
$model = new clsDichVu();

// Lấy action
$action = isset($_GET['action']) ? $_GET['action'] : 'danhsach';

// Xử lý các action
switch ($action) {
    case 'chitiet':
        // Xóa tất cả output trước đó
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Hiển thị chi tiết dịch vụ
        if (!isset($_GET['maDV'])) {
            header("Location: cDichVu.php");
            exit();
        }
        
        $maDV = $_GET['maDV'];
        $chiTiet = $model->layChiTietDichVu($maDV);
        
        if (!$chiTiet) {
            echo "<script>alert('Không tìm thấy dịch vụ!'); window.location.href='cDichVu.php';</script>";
            exit();
        }
        
        $soLuongKhaDung = $chiTiet['soLuongKhaDung'];
        $hinhAnh = array();
        
        include("../view/chiTietDichVu.php");
        break;
        
    case 'laythongtin':
        // Xóa tất cả output trước đó
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        // Lấy thông tin khách hàng (AJAX)
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if (isset($_GET['idKH'])) {
                $idKH = intval($_GET['idKH']);
                $thongTin = $model->layThongTinKhachHang($idKH);
                
                if ($thongTin) {
                    echo json_encode($thongTin);
                } else {
                    echo json_encode(array('error' => 'Không tìm thấy khách hàng với ID: ' . $idKH));
                }
            } else {
                echo json_encode(array('error' => 'Thiếu tham số idKH'));
            }
        } catch (Exception $e) {
            echo json_encode(array('error' => 'Lỗi server: ' . $e->getMessage()));
        }
        
        ob_end_flush();
        exit();
        
        
    case 'xacnhan':
        // Xóa tất cả output trước đó
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        // Xử lý xác nhận đặt dịch vụ (AJAX)
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Start session if not started (PHP 5.2 compatible)
                if (session_id() == '') {
                    session_start();
                }
                
                // Check if user is logged in
                if (!isset($_SESSION['idKH'])) {
                    echo json_encode(array('success' => false, 'message' => 'Vui lòng đăng nhập để đặt dịch vụ!'));
                    ob_end_flush();
                    exit();
                }
                
                // Get customer ID from session
                $idKH = intval($_SESSION['idKH']);
                $maDV = isset($_POST['maDV']) ? $_POST['maDV'] : '';
                $soLuong = isset($_POST['soLuong']) ? intval($_POST['soLuong']) : 0;
                
                if (empty($maDV) || $soLuong <= 0) {
                    echo json_encode(array('success' => false, 'message' => 'Thông tin không hợp lệ!'));
                    ob_end_flush();
                    exit();
                }
                
                $result = $model->xacNhanDatDichVu($idKH, $maDV, $soLuong);
                error_log("=== BOOKING RESULT ===");
                error_log("Result: " . json_encode($result));
                error_log("======================");
                echo json_encode($result);
            } else {
                echo json_encode(array('success' => false, 'message' => 'Phương thức request không hợp lệ'));
            }
        } catch (Exception $e) {
            error_log("=== EXCEPTION CAUGHT ===");
            error_log("Error: " . $e->getMessage());
            error_log("========================");
            echo json_encode(array('success' => false, 'message' => 'Lỗi: ' . $e->getMessage()));
        }
        
        ob_end_flush();
        exit();
        
        
    default:
        // Xóa tất cả output trước đó
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Hiển thị danh sách dịch vụ
        $loaiDV = isset($_GET['loaiDV']) ? $_GET['loaiDV'] : null;
        $giaMin = isset($_GET['giaMin']) ? $_GET['giaMin'] : null;
        $giaMax = isset($_GET['giaMax']) ? $_GET['giaMax'] : null;
        
        $danhSachLoai = $model->layDanhSachLoai();
        $danhSachDichVu = $model->layDanhSachDichVu($loaiDV, $giaMin, $giaMax);
        
        include("../view/dangkyDichVuBoSung.php");
        break;
}

ob_end_flush(); // Kết thúc output buffering
?>
