<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../model/clsLichSuGD.php");

$model = new clsLichSuGD();
$action = isset($_GET['action']) ? $_GET['action'] : 'danhsach';

switch ($action) {
    case 'chitiet':
        // Xem chi tiết hóa đơn
        if (!isset($_GET['maHD'])) {
            header("Location: cLichSuGD.php");
            exit();
        }
        
        $maHD = $_GET['maHD'];
        $hoaDon = $model->layChiTietHoaDon($maHD);
        
        if (!$hoaDon) {
            echo "<script>alert('Không tìm thấy hóa đơn!'); window.location.href='cLichSuGD.php';</script>";
            exit();
        }
        
        include("../view/chiTietHoaDon.php");
        break;
        
    default:
        // Hiển thị danh sách lịch sử
        $tuNgay = isset($_GET['tuNgay']) ? $_GET['tuNgay'] : null;
        $denNgay = isset($_GET['denNgay']) ? $_GET['denNgay'] : null;
        
        $danhSachHoaDon = $model->layLichSuGiaoDich($tuNgay, $denNgay);
        
        include("../view/xemLichSuGD.php");
        break;
}
?>