<?php
require_once("../model/clsThanhToan.php");

$action = isset($_GET['action']) ? $_GET['action'] : 'danhsach';

$model = new clsThanhToan();

switch ($action) {

    case 'danhsach':
        $danhSachHoaDon = $model->layDanhSachHoaDonChuaThanhToan();
        include("../view/thanhToan.php");
        break;

    case 'chitiet':
        if (!isset($_GET['maHD'])) {
            header("Location: cThanhToan.php");
            exit;
        }
        $maHD = $_GET['maHD'];
        $hoaDon = $model->layChiTietHoaDon($maHD);
        $maGiaoDich = $model->taoMaGiaoDich();
        include("../view/chiTietThanhToan.php");
        break;

    case 'xacnhan':
        header('Content-Type: application/json; charset=utf-8');
        $maHD = $_POST['maHD'];
        $maGD = $_POST['maGD'];
        $phuongThuc = $_POST['phuongThuc'];
        
        if ($phuongThuc == 'TienMat') {
            $kq = $model->thanhToanTienMat($maHD);
        } else {
            $kq = $model->thanhToanChuyenKhoan($maHD, $maGD);
        }
        
        $kq['maGD'] = $maGD;
        echo json_encode($kq);
        exit;

    case 'tienmat':
        header('Content-Type: application/json; charset=utf-8');
        $maHD = $_POST['maHD'];
        $kq = $model->thanhToanTienMat($maHD);
        echo json_encode($kq);
        exit;

    case 'chuyenkhoan':
        header('Content-Type: application/json; charset=utf-8');
        $maHD = $_POST['maHD'];
        $maGD = $_POST['maGiaoDich'];
        $kq = $model->thanhToanChuyenKhoan($maHD, $maGD);
        echo json_encode($kq);
        exit;

    default:
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success' => false, 'message' => 'Action không hợp lệ!'));
        exit;
}
?>
