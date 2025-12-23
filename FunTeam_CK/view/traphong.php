<?php
// BẬT ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION["dn"]) || $_SESSION["dn"] !== true) {
    header("Location: login.php");
    exit();
}
$isLoggedIn = isset($_SESSION["dn"]) && $_SESSION["dn"] === true;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';
// Lấy thông tin người dùng từ session
$hoTen = "";
if (isset($_SESSION["hoTen"]) && !empty($_SESSION["hoTen"])) {
    $hoTen = $_SESSION["hoTen"];
}

// Khai báo biến
$ketQuaTimKiem = array();
$chiTietGiaoDich = null;
$tuKhoa = "";
$thongBao = "";
$maDDPView = "";
$showThanhToan = false;
$phuongThucTT = "Tiền mặt";
$noiDungChuyenKhoan = "";

// Include controller
include_once("../controller/cTraPhong.php");

// Kiểm tra controller có tồn tại không
if (!class_exists('controlTraPhong')) {
    die("Lỗi: Không thể tải controller!");
}

$controller = new controlTraPhong();

// Xử lý tìm kiếm
if (isset($_POST["btnTimKiem"]) && isset($_POST["txtTimKiem"])) {
    $txtTimKiem = trim($_POST["txtTimKiem"]);
    if (!empty($txtTimKiem)) {
        $tuKhoa = $txtTimKiem;
        $ketQuaTimKiem = $controller->cTimKiemGiaoDich($tuKhoa);
    }
}

// Xử lý xem chi tiết
if (isset($_GET["xemchitiet"]) && !empty($_GET["xemchitiet"])) {
    $maDDPView = trim($_GET["xemchitiet"]);
    $chiTietGiaoDich = $controller->cLayChiTietGiaoDich($maDDPView);
}

// Xử lý hiển thị thanh toán
if (isset($_POST["btnHienThiThanhToan"]) && isset($_POST["maDDP"])) {
    $maDDP = trim($_POST["maDDP"]);
    $maDDPView = $maDDP;
    $chiTietGiaoDich = $controller->cLayChiTietGiaoDich($maDDP);
    $showThanhToan = true;
}

// Xử lý xác nhận trả phòng
if (isset($_POST["btnXacNhanTraPhong"]) && isset($_POST["maDDP"])) {
    $maDDP = trim($_POST["maDDP"]);
    $phuongThucTT = isset($_POST["phuongThucTT"]) ? $_POST["phuongThucTT"] : "Tiền mặt";
    $noiDungChuyenKhoan = isset($_POST["noiDungChuyenKhoan"]) ? $_POST["noiDungChuyenKhoan"] : "";
    
    $result = $controller->cXacNhanTraPhong($maDDP, $phuongThucTT, $noiDungChuyenKhoan);
    
    if ($result['success']) {
        $thongBao = '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            ' . $result['message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
        // Reset sau khi trả phòng thành công
        $chiTietGiaoDich = null;
        $showThanhToan = false;
    } else {
        $thongBao = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            ' . $result['message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Trả Phòng</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* GIỐNG HỆT HUY GIAO DỊCH */
        :root {
            --primary-color: #0d22ac;
            --primary-light: #2d3fd8;
            --primary-dark: #07157a;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --danger-dark: #b02a37;
            --danger-light: #e4606d;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --gray-light: #e9ecef;
            --gray-medium: #dee2e6;
            --gray-dark: #6c757d;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        
        /* Navbar */
        .navbar-custom {
            background: var(--primary-color);
            box-shadow: 0 2px 10px rgba(13, 34, 172, 0.2);
            padding: 0.8rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
            padding-left: 1rem;
        }
        
        .navbar-brand i {
            color: #ffd700;
            margin-right: 8px;
        }
        
        .user-info {
            color: white;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            margin-right: 1rem;
        }
        
        .user-info i {
            margin-right: 8px;
            font-size: 1.1rem;
        }
        
        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        /* Card */
        .main-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            background: white;
            margin-bottom: 30px;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 1.5rem 2rem;
        }
        
        .card-header-custom h3 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .card-header-custom p {
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        /* Search Box */
        .search-container {
            padding: 2rem;
            border-bottom: 1px solid var(--gray-medium);
        }
        
        .search-box {
            position: relative;
        }
        
        .search-input {
            border: 2px solid var(--gray-medium);
            border-radius: 50px;
            padding: 15px 25px;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(13, 34, 172, 0.1);
            outline: none;
        }
        
        .search-btn {
            position: absolute;
            right: 5px;
            top: 5px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .search-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 34, 172, 0.2);
        }
        
        /* Results */
        .results-container {
            padding: 0 2rem 2rem;
        }
        
        .results-title {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .results-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .search-term {
            color: var(--primary-color);
            font-weight: 700;
        }
        
        /* Result Item */
        .result-item {
            background: white;
            border: 1px solid var(--gray-medium);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
            border-left: 4px solid var(--primary-color);
        }
        
        .result-item:hover {
            border-left-color: var(--success-color);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
        
        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .result-code {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary-dark);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-received {
            background: rgba(23, 162, 184, 0.1);
            color: var(--info-color);
            border: 1px solid rgba(23, 162, 184, 0.2);
        }
        
        .status-confirmed {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .status-pending {
            background: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
            border: 1px solid rgba(255, 193, 7, 0.2);
        }
        
        .result-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .info-row {
            display: flex;
            align-items: center;
        }
        
        .info-row i {
            width: 24px;
            color: var(--primary-color);
            margin-right: 10px;
        }
        
        .btn-view {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
        }
        
        .btn-view:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 34, 172, 0.2);
            text-decoration: none;
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--secondary-color);
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--gray-light);
        }
        
        /* Detail Sections */
        .detail-container {
            padding: 2rem;
        }
        
        .section-card {
            border: 1px solid var(--gray-medium);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            background: white;
        }
        
        .section-title {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--gray-light);
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }
        
        .info-label i {
            margin-right: 8px;
            font-size: 0.9rem;
            color: var(--primary-color);
        }
        
        .info-value {
            color: #555;
            font-size: 1.05rem;
        }
        
        .info-value-highlight {
            color: var(--primary-color);
            font-weight: 700;
        }
        
        /* Thanh toán */
        .payment-methods {
            display: flex;
            gap: 15px;
            margin: 20px 0;
        }
        
        .payment-method {
            flex: 1;
            border: 2px solid var(--gray-medium);
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .payment-method:hover {
            border-color: var(--primary-light);
        }
        
        .payment-method.selected {
            border-color: var(--primary-color);
            background: #f0f7ff;
        }
        
        .bank-info {
            background: #f8f9fa;
            border: 1px solid var(--gray-medium);
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }
        
        /* Nút thanh toán */
        .btn-payment {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.05rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(13, 34, 172, 0.3);
            cursor: pointer;
        }
        
        .btn-payment:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #051055 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(13, 34, 172, 0.4);
        }
        
        .btn-payment:disabled {
            background: var(--gray-dark) !important;
            color: var(--light-color) !important;
            opacity: 0.7;
            cursor: not-allowed;
            box-shadow: none !important;
            transform: none !important;
        }
        
        /* Money Format */
        .money {
            color: var(--success-color);
            font-weight: 600;
        }
        
        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--danger-color);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 0 15px;
                margin: 20px auto;
            }
            
            .search-box {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            
            .search-input {
                width: 100%;
                padding-right: 15px;
                margin-bottom: 0;
            }
            
            .search-btn {
                position: relative;
                width: 100%;
                right: 0;
                top: 0;
                margin-top: 5px;
            }
            
            .card-header-custom {
                padding: 1.2rem 1.5rem;
            }
            
            .search-container,
            .detail-container {
                padding: 1.5rem;
            }
            
            .section-card {
                padding: 20px;
            }
            
            .user-info span {
                display: none;
            }
            
            .user-info {
                padding: 8px;
            }
            
            .payment-methods {
                flex-direction: column;
            }
        }
        
        @media (max-width: 576px) {
            .result-info,
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-hotel"></i>Quản Lý Khách Sạn
            </a>
            <div class="d-flex align-items-center">
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Xin chào, <?php echo htmlspecialchars($username); ?></span>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <!-- Card chính -->
        <div class="main-card">
            <!-- Card Header -->
            <div class="card-header-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3><i class="fas fa-door-open me-2"></i>Quản lý Trả Phòng</h3>
                        <p>Tìm kiếm và xác nhận trả phòng cho khách hàng</p>
                    </div>
                    <a href="dashboard_letan.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
            </div>
            
            <!-- Card Body -->
            <div class="card-body p-0">
                <!-- Form tìm kiếm -->
                <div class="search-container">
                    <form method="POST" action="">
                        <div class="search-box">
                            <input type="text" name="txtTimKiem" class="search-input" 
                                   placeholder="Nhập mã đơn, CCCD, số điện thoại hoặc tên khách hàng..." 
                                   value="<?php echo htmlspecialchars($tuKhoa); ?>" required>
                            <button type="submit" name="btnTimKiem" class="search-btn">
                                <i class="fas fa-search me-2"></i>Tìm Kiếm
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Hiển thị kết quả tìm kiếm -->
                <?php if (isset($_POST["btnTimKiem"])): ?>
                    <div class="results-container">
                        <h5 class="results-title">
                            <i class="fas fa-search me-2"></i>Kết quả tìm kiếm 
                            <?php if ($tuKhoa): ?>
                                cho "<span class="search-term"><?php echo htmlspecialchars($tuKhoa); ?></span>"
                            <?php endif; ?>
                        </h5>
                        
                        <?php if (count($ketQuaTimKiem) > 0): ?>
                            <div class="results-list">
                                <?php foreach ($ketQuaTimKiem as $row): ?>
                                    <div class="result-item">
                                        <div class="result-header">
                                            <div class="result-code"><?php echo htmlspecialchars($row['maDDP']); ?></div>
                                            <span class="status-badge 
                                                <?php echo ($row['trangThai'] == 'DaNhan') ? 'status-confirmed' : 
                                                       (($row['trangThai'] == 'DaTra') ? 'status-cancelled' : 'status-pending'); ?>">
                                                <?php echo htmlspecialchars($row['trangThai']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="result-info">
                                            <div class="info-row">
                                                <i class="fas fa-user"></i>
                                                <span><?php echo htmlspecialchars($row['hoTen']); ?></span>
                                            </div>
                                            <div class="info-row">
                                                <i class="fas fa-phone"></i>
                                                <span><?php echo htmlspecialchars($row['soDienThoai']); ?></span>
                                            </div>
                                            <div class="info-row">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span>Nhận phòng: <?php echo date('d/m/Y', strtotime($row['ngayNhanPhong'])); ?></span>
                                            </div>
                                            <div class="info-row">
                                                <i class="fas fa-bed"></i>
                                                <span>Số phòng: <?php echo htmlspecialchars($row['soPhong']); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="text-end mt-3">
                                            <a href="?xemchitiet=<?php echo urlencode($row['maDDP']); ?>" 
                                               class="btn btn-view">
                                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-search fa-3x"></i>
                                <h4 class="mt-3">Không tìm thấy kết quả</h4>
                                <p>Không có giao dịch nào ở trạng thái "Đã nhận phòng" phù hợp với từ khóa tìm kiếm</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Hiển thị chi tiết giao dịch -->
                <?php if (!empty($maDDPView)): ?>
                    <div class="detail-container">
                        <?php if ($chiTietGiaoDich && !empty($chiTietGiaoDich['donDatPhong'])): ?>
                            <!-- Debug info -->
                            <div class="alert alert-info alert-dismissible fade show mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Đang xem chi tiết đơn: <strong><?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['maDDP']); ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            
                            <!-- Thông tin đơn đặt phòng -->
                            <div class="section-card">
                                <h4 class="section-title">
                                    <i class="fas fa-calendar-alt me-2"></i>Thông tin đơn đặt phòng
                                </h4>
                                
                                <div class="info-grid">
                                    <div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-tag"></i>Mã đơn đặt
                                            </div>
                                            <div class="info-value info-value-highlight"><?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['maDDP']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-calendar-plus"></i>Ngày đặt
                                            </div>
                                            <div class="info-value"><?php echo date('d/m/Y', strtotime($chiTietGiaoDich['donDatPhong']['ngayDatPhong'])); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-calendar-check"></i>Ngày nhận
                                            </div>
                                            <div class="info-value"><?php echo date('d/m/Y', strtotime($chiTietGiaoDich['donDatPhong']['ngayNhanPhong'])); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-calendar-minus"></i>Ngày trả
                                            </div>
                                            <div class="info-value"><?php echo date('d/m/Y', strtotime($chiTietGiaoDich['donDatPhong']['ngayTraPhong'])); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-hashtag"></i>Số lượng phòng
                                            </div>
                                            <div class="info-value">
                                                <?php 
                                                $soPhongThucTe = isset($chiTietGiaoDich['donDatPhong']['soLuongPhongThucTe']) ? 
                                                    $chiTietGiaoDich['donDatPhong']['soLuongPhongThucTe'] : 
                                                    count($chiTietGiaoDich['chiTietPhong']);
                                                echo htmlspecialchars($soPhongThucTe) . ' phòng';
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-clock"></i>Số ngày ở
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['soNgay']); ?> ngày</div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-circle"></i>Trạng thái
                                            </div>
                                            <span class="status-badge 
                                                <?php echo ($chiTietGiaoDich['donDatPhong']['trangThai'] == 'DaNhan') ? 'status-confirmed' : 
                                                       (($chiTietGiaoDich['donDatPhong']['trangThai'] == 'DaTra') ? 'status-cancelled' : 
                                                       (($chiTietGiaoDich['donDatPhong']['trangThai'] == 'DaHuy') ? 'status-cancelled' : 'status-pending')); ?>">
                                                <?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['trangThai']); ?>
                                            </span>
                                        </div>
                                        
                                        <?php if (!empty($chiTietGiaoDich['donDatPhong']['ghiChu'])): ?>
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-sticky-note"></i>Ghi chú
                                                </div>
                                                <div class="info-value"><?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['ghiChu']); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Thông tin khách hàng -->
                            <div class="section-card">
                                <h4 class="section-title">
                                    <i class="fas fa-user me-2"></i>Thông tin khách hàng
                                </h4>
                                
                                <div class="info-grid">
                                    <div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-user"></i>Họ tên
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietGiaoDich['khachHang']['hoTen']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-id-card"></i>CCCD
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietGiaoDich['khachHang']['CCCD']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-envelope"></i>Email
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietGiaoDich['khachHang']['email']); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-phone"></i>Số điện thoại
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietGiaoDich['khachHang']['soDienThoai']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-map-marker-alt"></i>Địa chỉ
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietGiaoDich['khachHang']['diaChi']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-crown"></i>Loại khách hàng
                                            </div>
                                            <div class="info-value">
                                                <?php echo ($chiTietGiaoDich['khachHang']['loaiKH'] == 'VIP') ? 
                                                    '<span class="text-warning fw-bold">VIP</span>' : 'Thường'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Chi tiết phòng đã đặt -->
                            <?php if (!empty($chiTietGiaoDich['chiTietPhong'])): ?>
                                <div class="section-card">
                                    <h4 class="section-title">
                                        <i class="fas fa-bed me-2"></i>Chi tiết phòng đã đặt
                                    </h4>
                                    
                                    <div class="row">
                                        <?php foreach ($chiTietGiaoDich['chiTietPhong'] as $phong): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="room-item">
                                                    <div class="mb-2">
                                                        <strong>Phòng <?php echo htmlspecialchars($phong['soPhong']); ?></strong>
                                                        <span class="badge bg-<?php echo $phong['tinhTrang'] == 'Đang ở' ? 'success' : 'secondary'; ?> float-end">
                                                            <?php echo htmlspecialchars($phong['tinhTrang']); ?>
                                                        </span>
                                                    </div>
                                                    <div>Tầng: <?php echo htmlspecialchars($phong['tangPhong']); ?></div>
                                                    <div>Loại: <?php echo htmlspecialchars($phong['tenLoaiPhong']); ?></div>
                                                    <div>Sức chứa: <?php echo htmlspecialchars($phong['sucChua']); ?> người</div>
                                                    <div>Giá/ngày: <span class="money"><?php echo number_format($phong['giaPhong'], 0, ',', '.'); ?> VNĐ</span></div>
                                                    <div class="mt-2">
                                                        <strong>Tiền phòng (<?php echo $chiTietGiaoDich['soNgay']; ?> ngày):</strong><br>
                                                        <span class="money"><?php echo number_format($phong['tienPhong'], 0, ',', '.'); ?> VNĐ</span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Tổng tiền phòng:</strong> 
                                        <?php echo number_format($chiTietGiaoDich['tongTienPhong'], 0, ',', '.'); ?> VNĐ
                                        (<?php echo $chiTietGiaoDich['soNgay']; ?> ngày)
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Dịch vụ đã sử dụng -->
                            <?php if (!empty($chiTietGiaoDich['dichVu'])): ?>
                                <div class="section-card">
                                    <h4 class="section-title">
                                        <i class="fas fa-concierge-bell me-2"></i>Dịch vụ đã sử dụng
                                    </h4>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Mã DV</th>
                                                    <th>Tên dịch vụ</th>
                                                    <th>Số lượng</th>
                                                    <th>Đơn giá</th>
                                                    <th>Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($chiTietGiaoDich['dichVu'] as $dv): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($dv['maDV']); ?></td>
                                                        <td><?php echo htmlspecialchars($dv['tenDV']); ?></td>
                                                        <td><?php echo htmlspecialchars($dv['soLuong']); ?></td>
                                                        <td class="money"><?php echo number_format($dv['donGia'], 0, ',', '.'); ?> VNĐ</td>
                                                        <td class="money"><?php echo number_format($dv['thanhTien'], 0, ',', '.'); ?> VNĐ</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tr class="table-info">
                                                    <td colspan="4" class="text-end"><strong>Tổng tiền dịch vụ:</strong></td>
                                                    <td class="money"><strong><?php echo number_format($chiTietGiaoDich['tongTienDichVu'], 0, ',', '.'); ?> VNĐ</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Bồi thường (nếu có) -->
                            <?php if (!empty($chiTietGiaoDich['boiThuong'])): ?>
                                <div class="section-card">
                                    <h4 class="section-title">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Bồi thường
                                    </h4>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Mã BT</th>
                                                    <th>Ngày</th>
                                                    <th>Lý do</th>
                                                    <th>Số tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($chiTietGiaoDich['boiThuong'] as $bt): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($bt['maBT']); ?></td>
                                                        <td><?php echo date('d/m/Y', strtotime($bt['ngayBT'])); ?></td>
                                                        <td><?php echo htmlspecialchars($bt['lyDo']); ?></td>
                                                        <td class="money"><?php echo number_format($bt['tongBoiThuong'], 0, ',', '.'); ?> VNĐ</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tr class="table-warning">
                                                    <td colspan="3" class="text-end"><strong>Tổng bồi thường:</strong></td>
                                                    <td class="money"><strong><?php echo number_format($chiTietGiaoDich['tongBoiThuong'], 0, ',', '.'); ?> VNĐ</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Khuyến mãi (nếu có) -->
                            <?php if (isset($chiTietGiaoDich['khuyenMai'])): ?>
                                <div class="section-card">
                                    <h4 class="section-title">
                                        <i class="fas fa-percentage me-2"></i>Khuyến mãi
                                    </h4>
                                    
                                    <div class="alert alert-success">
                                        <i class="fas fa-gift me-2"></i>
                                        <strong>Mã khuyến mãi:</strong> <?php echo htmlspecialchars($chiTietGiaoDich['khuyenMai']['maKM']); ?>
                                        <br>
                                        <strong>Mức giảm:</strong> <?php echo htmlspecialchars($chiTietGiaoDich['khuyenMai']['mucGiam']); ?>%
                                        <br>
                                        <strong>Tiền giảm giá:</strong> 
                                        <span class="money"><?php echo number_format($chiTietGiaoDich['tienGiamGia'], 0, ',', '.'); ?> VNĐ</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Tổng thanh toán -->
                            <div class="section-card">
                                <h4 class="section-title">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>Tổng thanh toán
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Tiền phòng (<?php echo $chiTietGiaoDich['soNgay']; ?> ngày):</div>
                                            <div class="info-value money"><?php echo number_format($chiTietGiaoDich['tongTienPhong'], 0, ',', '.'); ?> VNĐ</div>
                                        </div>
                                        
                                        <?php if ($chiTietGiaoDich['tongTienDichVu'] > 0): ?>
                                            <div class="info-item">
                                                <div class="info-label">Tiền dịch vụ:</div>
                                                <div class="info-value money"><?php echo number_format($chiTietGiaoDich['tongTienDichVu'], 0, ',', '.'); ?> VNĐ</div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($chiTietGiaoDich['tongBoiThuong'] > 0): ?>
                                            <div class="info-item">
                                                <div class="info-label">Tiền bồi thường:</div>
                                                <div class="info-value money"><?php echo number_format($chiTietGiaoDich['tongBoiThuong'], 0, ',', '.'); ?> VNĐ</div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($chiTietGiaoDich['tienGiamGia']) && $chiTietGiaoDich['tienGiamGia'] > 0): ?>
                                            <div class="info-item text-success">
                                                <div class="info-label">Giảm giá:</div>
                                                <div class="info-value money">-<?php echo number_format($chiTietGiaoDich['tienGiamGia'], 0, ',', '.'); ?> VNĐ</div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="info-item mt-3 pt-3 border-top">
                                            <div class="info-label" style="font-size: 1.2rem;">TỔNG CỘNG:</div>
                                            <div class="info-value total-amount"><?php echo number_format($chiTietGiaoDich['tongTien'], 0, ',', '.'); ?> VNĐ</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form thanh toán -->
                            <?php if (!$showThanhToan): ?>
                                <div class="section-card">
                                    <h4 class="section-title">
                                        <i class="fas fa-cash-register me-2"></i>Thanh toán và trả phòng
                                    </h4>
                                    
                                    <?php 
                                    // Kiểm tra có thể trả phòng không
                                    $coTheTraPhong = $controller->cKiemTraCoTheTraPhong($maDDPView);
                                    ?>
                                    
                                    <?php if ($coTheTraPhong): ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="maDDP" value="<?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['maDDP']); ?>">
                                            
                                            <button type="submit" name="btnHienThiThanhToan" class="btn btn-payment">
                                                <i class="fas fa-credit-card me-2"></i>TIẾN HÀNH THANH TOÁN VÀ TRẢ PHÒNG
                                            </button>
                                            
                                            <div class="alert alert-info mt-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Lưu ý:</strong> Thao tác này sẽ:
                                                <ul class="mt-2">
                                                    <li>Cập nhật trạng thái đơn thành "Đã trả phòng"</li>
                                                    <li>Cập nhật trạng thái phòng về "Trống"</li>
                                                    <li>Tạo hoặc cập nhật hóa đơn thanh toán</li>
                                                    <li>Hoàn tất quy trình trả phòng</li>
                                                </ul>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>KHÔNG THỂ TRẢ PHÒNG</strong><br>
                                            Đơn đặt phòng không ở trạng thái "Đã nhận phòng". 
                                            Trạng thái hiện tại: <strong><?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['trangThai']); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <!-- Form chọn phương thức thanh toán -->
                                <div class="section-card">
                                    <h4 class="section-title">
                                        <i class="fas fa-credit-card me-2"></i>Chọn phương thức thanh toán
                                    </h4>
                                    
                                    <form method="POST" action="" id="formThanhToan">
                                        <input type="hidden" name="maDDP" value="<?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['maDDP']); ?>">
                                        
                                        <div class="payment-methods">
                                            <div class="payment-method <?php echo $phuongThucTT == 'Tiền mặt' ? 'selected' : ''; ?>" 
                                                 onclick="selectPaymentMethod('Tiền mặt')">
                                                <input type="radio" name="phuongThucTT" value="Tiền mặt" 
                                                       id="tienMat" <?php echo $phuongThucTT == 'Tiền mặt' ? 'checked' : ''; ?> required hidden>
                                                <label for="tienMat" style="cursor: pointer; width: 100%;">
                                                    <div class="text-center">
                                                        <i class="fas fa-money-bill-wave fa-2x mb-2 text-success"></i>
                                                        <h5>Tiền mặt</h5>
                                                        <p class="text-muted mb-0">Thanh toán trực tiếp tại quầy</p>
                                                    </div>
                                                </label>
                                            </div>
                                            
                                            <div class="payment-method <?php echo $phuongThucTT == 'Chuyển khoản' ? 'selected' : ''; ?>" 
                                                 onclick="selectPaymentMethod('Chuyển khoản')">
                                                <input type="radio" name="phuongThucTT" value="Chuyển khoản" 
                                                       id="chuyenKhoan" <?php echo $phuongThucTT == 'Chuyển khoản' ? 'checked' : ''; ?> required hidden>
                                                <label for="chuyenKhoan" style="cursor: pointer; width: 100%;">
                                                    <div class="text-center">
                                                        <i class="fas fa-university fa-2x mb-2 text-primary"></i>
                                                        <h5>Chuyển khoản</h5>
                                                        <p class="text-muted mb-0">Thanh toán qua ngân hàng</p>
                                                    </div>
                                                </label>
                                            </div>
                                            
                                            <div class="payment-method <?php echo $phuongThucTT == 'Thẻ tín dụng' ? 'selected' : ''; ?>" 
                                                 onclick="selectPaymentMethod('Thẻ tín dụng')">
                                                <input type="radio" name="phuongThucTT" value="Thẻ tín dụng" 
                                                       id="theTinDung" <?php echo $phuongThucTT == 'Thẻ tín dụng' ? 'checked' : ''; ?> required hidden>
                                                <label for="theTinDung" style="cursor: pointer; width: 100%;">
                                                    <div class="text-center">
                                                        <i class="fas fa-credit-card fa-2x mb-2 text-warning"></i>
                                                        <h5>Thẻ tín dụng</h5>
                                                        <p class="text-muted mb-0">Visa/Mastercard/JCB</p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <!-- Thông tin chuyển khoản (chỉ hiện khi chọn chuyển khoản) -->
                                        <div id="bankInfo" style="display: <?php echo $phuongThucTT == 'Chuyển khoản' ? 'block' : 'none'; ?>;">
                                            <?php 
                                            $bankInfo = $controller->cLayThongTinNganHang();
                                            ?>
                                            <div class="bank-info mt-3">
                                                <h5><i class="fas fa-university me-2"></i>Thông tin chuyển khoản</h5>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="info-item">
                                                            <div class="info-label">Ngân hàng:</div>
                                                            <div class="info-value"><?php echo htmlspecialchars($bankInfo['nganHang']); ?></div>
                                                        </div>
                                                        <div class="info-item">
                                                            <div class="info-label">Số tài khoản:</div>
                                                            <div class="info-value info-value-highlight"><?php echo htmlspecialchars($bankInfo['soTaiKhoan']); ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="info-item">
                                                            <div class="info-label">Chủ tài khoản:</div>
                                                            <div class="info-value"><?php echo htmlspecialchars($bankInfo['chuTaiKhoan']); ?></div>
                                                        </div>
                                                        <div class="info-item">
                                                            <div class="info-label">Chi nhánh:</div>
                                                            <div class="info-value"><?php echo htmlspecialchars($bankInfo['chiNhanh']); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="info-item mt-3">
                                                    <div class="info-label">Nội dung chuyển khoản:</div>
                                                    <input type="text" name="noiDungChuyenKhoan" 
                                                           class="form-control mt-2" 
                                                           placeholder="Ví dụ: <?php echo htmlspecialchars($chiTietGiaoDich['khachHang']['hoTen']); ?> - <?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['maDDP']); ?>"
                                                           value="<?php echo htmlspecialchars($noiDungChuyenKhoan); ?>"
                                                           required>
                                                    <small class="text-muted">Vui lòng nhập đúng nội dung để xác nhận thanh toán</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Tổng thanh toán -->
                                        <div class="alert alert-success mt-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-1"><i class="fas fa-file-invoice-dollar me-2"></i>Tổng số tiền thanh toán</h5>
                                                    <p class="mb-0">Sau khi xác nhận, hệ thống sẽ hoàn tất quy trình trả phòng</p>
                                                </div>
                                                <div class="text-end">
                                                    <div class="total-amount"><?php echo number_format($chiTietGiaoDich['tongTien'], 0, ',', '.'); ?> VNĐ</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mt-4">
                                            <button type="button" class="btn btn-secondary" onclick="window.location.href='?xemchitiet=<?php echo urlencode($maDDPView); ?>'">
                                                <i class="fas fa-arrow-left me-2"></i>Quay lại chi tiết
                                            </button>
                                            
                                            <button type="submit" name="btnXacNhanTraPhong" class="btn btn-payment" onclick="return confirmThanhToan();">
                                                <i class="fas fa-check-circle me-2"></i>XÁC NHẬN THANH TOÁN VÀ TRẢ PHÒNG
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <!-- Không tìm thấy chi tiết -->
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <h4 class="text-danger">Không tìm thấy thông tin</h4>
                                <p class="text-muted">Giao dịch <strong><?php echo htmlspecialchars($maDDPView); ?></strong> không tồn tại</p>
                                <a href="traphong.php" class="btn btn-view mt-3">
                                    <i class="fas fa-arrow-left me-1"></i>Quay lại tìm kiếm
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Hàm chọn phương thức thanh toán
        function selectPaymentMethod(method) {
            // Ẩn tất cả các phương thức
            var paymentMethods = document.querySelectorAll('.payment-method');
            paymentMethods.forEach(function(el) {
                el.classList.remove('selected');
            });
            
            // Hiển thị phương thức được chọn
            event.currentTarget.classList.add('selected');
            
            // Cập nhật radio button
            var radio = event.currentTarget.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Hiển thị/ẩn thông tin chuyển khoản
            var bankInfo = document.getElementById('bankInfo');
            if (method === 'Chuyển khoản') {
                bankInfo.style.display = 'block';
            } else {
                bankInfo.style.display = 'none';
            }
        }
        
        // Hàm xác nhận thanh toán
        function confirmThanhToan() {
            var phuongThucTT = document.querySelector('input[name="phuongThucTT"]:checked').value;
            var message = 'BẠN CÓ CHẮC CHẮN MUỐN XÁC NHẬN THANH TOÁN VÀ TRẢ PHÒNG?\n\n';
            message += 'Phương thức: ' + phuongThucTT + '\n';
            message += 'Số tiền: ' + document.querySelector('.total-amount').textContent + '\n\n';
            message += 'Đơn đặt sẽ được cập nhật trạng thái "Đã trả phòng" và phòng sẽ được chuyển về trạng thái "Trống".';
            
            return confirm(message);
        }
        
        // Tự động cuộn đến phần chi tiết
        <?php if (!empty($maDDPView)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var detailSection = document.querySelector('.detail-container');
                if (detailSection) {
                    detailSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 300);
        });
        <?php endif; ?>
        
        // Tự động ẩn alert sau 5 giây
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('alert-dismissible')) {
                    var closeButton = alert.querySelector('.btn-close');
                    if (closeButton) {
                        closeButton.click();
                    }
                }
            });
        }, 5000);
        
        // Focus vào ô tìm kiếm khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            var searchInput = document.querySelector('input[name="txtTimKiem"]');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }
        });
    </script>
</body>
</html>