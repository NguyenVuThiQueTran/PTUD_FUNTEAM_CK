<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit;
}

require_once 'config/database.php';

try {
    // 1. Import các Model
    require_once dirname(__FILE__) . '/model/PhongModel.php';
    require_once dirname(__FILE__) . '/model/KhachHangModel.php';
    require_once dirname(__FILE__) . '/model/NhanVienModel.php';
    require_once dirname(__FILE__) . '/model/DichVuModel.php';
    
    // --- THÊM MODEL KHUYẾN MÃI ---
   // Import Model Khuyến Mãi
    if(file_exists(dirname(__FILE__) . '/model/KhuyenMaiModel.php')) {
        require_once dirname(__FILE__) . '/model/KhuyenMaiModel.php';
    }

    $phongModel = new PhongModel();
    $khModel = new KhachHangModel();
    $nvModel = new NhanVienModel();
    $dvModel = new DichVuModel();
    // Khởi tạo model khuyến mãi
    $kmModel = (class_exists('KhuyenMaiModel')) ? new KhuyenMaiModel() : null;

    // 2. Lấy số liệu thống kê tổng quan (Thêm biến $totalKM)
    $listPhong = $phongModel->getDanhSachPhong();
    $totalPhong = count($listPhong);
    $totalKhach = count($khModel->getDanhSachKH());
    $totalNV = count($nvModel->getDanhSachNV());
    $totalDV = count($dvModel->getDanhSachDV());
    
    // Đếm số khuyến mãi
    $totalKM = ($kmModel) ? count($kmModel->getDanhSachKM()) : 0;

    // 3. Kết nối DB để chạy các câu lệnh SQL phức tạp
    $db = new Database();
    $conn = $db->getConnection();

    // --- A. BIỂU ĐỒ DOANH THU (Cột) ---
    $revenueData = array_fill(0, 12, 0); 
    // Chỉ tính các hóa đơn đã thanh toán
    $sqlRev = "SELECT MONTH(ngayLap) as thang, SUM(tongTien) as total 
               FROM hoadon 
               WHERE trangThai = 'DaThanhToan' OR trangThai = 'Đã thanh toán' 
               GROUP BY MONTH(ngayLap)";
    $stmtRev = $conn->prepare($sqlRev);
    $stmtRev->execute();
    $resultRev = $stmtRev->fetchAll(PDO::FETCH_ASSOC);
    if($resultRev){
        foreach($resultRev as $r){
            $m = (int)$r['thang'];
            if($m >= 1 && $m <= 12) $revenueData[$m - 1] = (float)$r['total'];
        }
    }

    // --- B. BIỂU ĐỒ TRẠNG THÁI PHÒNG (Tròn) ---
    $statPhong = array('DangO' => 0, 'DaDat' => 0, 'Trong' => 0, 'BaoTri' => 0);
    if ($listPhong) {
        foreach ($listPhong as $row) {
            $p = is_object($row) ? get_object_vars($row) : (array)$row;
            $found = false;
            foreach ($p as $val) {
                $v = trim((string)$val);
                if (strlen($v) < 2 || is_numeric($v)) continue;
                
                if (stripos($v, 'Tr') === 0 || stripos($v, 'rống') !== false) { 
                    $statPhong['Trong']++; $found = true; break; 
                }
                if (stripos($v, 'ang') !== false || stripos($v, 'khách') !== false) { 
                    $statPhong['DangO']++; $found = true; break; 
                }
                if (stripos($v, 'đặt') !== false || stripos($v, 'dat') !== false) { 
                    $statPhong['DaDat']++; $found = true; break; 
                }
                if (stripos($v, 'Bảo') !== false || stripos($v, 'trì') !== false) { 
                    $statPhong['BaoTri']++; $found = true; break; 
                }
            }
            if (!$found) $statPhong['BaoTri']++;
        }
    }

    // --- C. DỮ LIỆU BẢNG: ĐẶT PHÒNG GẦN ĐÂY ---
    $sqlRecent = "SELECT kh.hoTen as HoTen, p.soPhong as SoPhong, p.hangPhong as HangPhong, 
                         ddp.ngayNhanPhong as NgayNhan, ddp.ngayTraPhong as NgayTra, 
                         ddp.trangThai as TrangThai, kh.soDienThoai as DienThoai
                  FROM dondatphong ddp
                  LEFT JOIN khachhang kh ON ddp.idKH = kh.idKH
                  LEFT JOIN chitietdatphong ctdp ON ddp.maDDP = ctdp.maDDP
                  LEFT JOIN phong p ON ctdp.maPhong = p.maPhong
                  ORDER BY ddp.ngayDatPhong DESC LIMIT 6";
    
    $stmtRecent = $conn->prepare($sqlRecent);
    $stmtRecent->execute();
    $recentBookings = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

    // --- D. DỮ LIỆU BẢNG: PHÒNG ĐANG CÓ KHÁCH ---
    $sqlOccupied = "SELECT kh.hoTen as HoTen, p.soPhong as SoPhong, p.hangPhong as HangPhong, 
                           ddp.ngayNhanPhong as NgayNhan, ddp.ngayTraPhong as NgayTra, 
                           ddp.trangThai as TrangThai, kh.soDienThoai as DienThoai
                    FROM dondatphong ddp
                    LEFT JOIN khachhang kh ON ddp.idKH = kh.idKH
                    LEFT JOIN chitietdatphong ctdp ON ddp.maDDP = ctdp.maDDP
                    LEFT JOIN phong p ON ctdp.maPhong = p.maPhong
                    WHERE ddp.trangThai LIKE '%Nhận%' OR ddp.trangThai LIKE '%CheckIn%'
                    GROUP BY p.soPhong LIMIT 8";
                    
    $stmtOccupied = $conn->prepare($sqlOccupied);
    $stmtOccupied->execute();
    $currentOccupied = $stmtOccupied->fetchAll(PDO::FETCH_ASSOC);

    // --- E. DỮ LIỆU: CHECK-IN / CHECK-OUT HÔM NAY ---
    $todayChecks = array('checkIn' => array(), 'checkOut' => array());
    
    // Check-in
    $sqlIn = "SELECT kh.hoTen as HoTen, p.soPhong as SoPhong, ddp.ngayNhanPhong as Gio 
              FROM dondatphong ddp 
              LEFT JOIN khachhang kh ON ddp.idKH = kh.idKH 
              LEFT JOIN chitietdatphong ctdp ON ddp.maDDP = ctdp.maDDP 
              LEFT JOIN phong p ON ctdp.maPhong = p.maPhong 
              WHERE DATE(ddp.ngayNhanPhong) = CURDATE()"; 
    $stmtIn = $conn->prepare($sqlIn); 
    $stmtIn->execute(); 
    $todayChecks['checkIn'] = $stmtIn->fetchAll(PDO::FETCH_ASSOC);

    // Check-out
    $sqlOut = "SELECT kh.hoTen as HoTen, p.soPhong as SoPhong, ddp.ngayTraPhong as Gio 
               FROM dondatphong ddp 
               LEFT JOIN khachhang kh ON ddp.idKH = kh.idKH 
               LEFT JOIN chitietdatphong ctdp ON ddp.maDDP = ctdp.maDDP 
               LEFT JOIN phong p ON ctdp.maPhong = p.maPhong 
               WHERE DATE(ddp.ngayTraPhong) = CURDATE()";
    $stmtOut = $conn->prepare($sqlOut); 
    $stmtOut->execute(); 
    $todayChecks['checkOut'] = $stmtOut->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $ex) {
    $totalPhong = $totalKhach = $totalNV = $totalDV = 0;
    $revenueData = array_fill(0, 12, 0);
    $statPhong = array('DangO'=>0, 'DaDat'=>0, 'Trong'=>0, 'BaoTri'=>0);
    $recentBookings = array();
    $currentOccupied = array();
    $todayChecks = array('checkIn' => array(), 'checkOut' => array());
}

// Gọi Header
include 'view/layouts/header.php';

// Router điều hướng
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
switch ($page) {
    case 'phong': include 'controller/phongController.php'; break;
    case 'nhanvien': include 'controller/nhanvienController.php'; break;
    case 'khachhang': include 'controller/khachhangController.php'; break;
    case 'dichvu': include 'controller/dichvuController.php'; break;
    
    // --- THÊM ROUTER KHUYẾN MÃI ---
    case 'khuyenmai': include 'controller/khuyenmaiController.php'; break;
    
    default: include 'view/dashboard_home.php'; break;
}

// Gọi Footer
include 'view/layouts/footer.php';
?>