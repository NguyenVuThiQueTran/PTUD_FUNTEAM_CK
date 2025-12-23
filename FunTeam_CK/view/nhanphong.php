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

// Include controller
include_once("../controller/cNhanPhong.php");

// Kiểm tra controller có tồn tại không
if (!class_exists('controlNhanPhong')) {
    die("Lỗi: Không thể tải controller!");
}

$controller = new controlNhanPhong();

// Xử lý tìm kiếm
if (isset($_POST["btnTimKiem"]) && isset($_POST["txtTimKiem"])) {
    $txtTimKiem = trim($_POST["txtTimKiem"]);
    if (!empty($txtTimKiem)) {
        $tuKhoa = $txtTimKiem;
        $ketQuaTimKiem = $controller->cTimKiemGiaoDich($tuKhoa);
        
        // Lấy thêm danh sách phòng cho từng kết quả
        foreach ($ketQuaTimKiem as &$row) {
            $row['danhSachPhong'] = $controller->cLayDanhSachPhong($row['maDDP']);
        }
    }
}

// Xử lý xem chi tiết
if (isset($_GET["xemchitiet"]) && !empty($_GET["xemchitiet"])) {
    $maDDPView = trim($_GET["xemchitiet"]);
    $chiTietGiaoDich = $controller->cLayChiTietGiaoDich($maDDPView);
    
    // Tính toán tiền phòng nếu có thông tin đơn
    if ($chiTietGiaoDich && !empty($chiTietGiaoDich['donDatPhong'])) {
        $tienPhong = $controller->cTinhTienPhong($maDDPView);
        $chiTietGiaoDich['tienPhong'] = $tienPhong;
    }
}
// Xử lý xác nhận nhận phòng
if (isset($_POST["btnXacNhanNhanPhong"]) && isset($_POST["maDDP"])) {
    $maDDP = trim($_POST["maDDP"]);
    $result = $controller->cXacNhanNhanPhong($maDDP);
    
    if ($result['success']) {
        $thongBao = '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            ' . $result['message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
        // Reset chi tiết sau khi nhận phòng thành công
        $chiTietGiaoDich = null;
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
    <title>Quản lý Nhận Phòng</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
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
        
        .status-cancelled {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(220, 53, 69, 0.2);
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
        
        /* Room Items */
        .room-item {
            background: var(--gray-light);
            border: 1px solid var(--gray-medium);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .room-item:hover {
            background: #f0f7ff;
            border-color: var(--primary-light);
        }
        
        /* Confirm Button */
        .btn-confirm {
            background: linear-gradient(135deg, var(--success-color) 0%, #1e7e34 100%);
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
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            cursor: pointer;
        }
        
        .btn-confirm:hover:not(:disabled) {
            background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
        }
        
        .btn-confirm:disabled {
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
        <div class="main-card">
            <div class="card-header-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3><i class="fas fa-key me-2"></i>Quản lý Nhận Phòng</h3>
                        <p>Tìm kiếm và xác nhận nhận phòng cho khách hàng</p>
                    </div>
                    <a href="dashboard_letan.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <?php echo $thongBao; ?>

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
                            <span class="status-badge status-received">
                                <?php 
                                $trangThaiHienThi = array(
                                    'DaNhan' => 'Đã nhận phòng',
                                    'DaTra' => 'Đã trả phòng',
                                    'DaHuy' => 'Đã hủy',
                                    'DangCho' => 'Đang chờ'
                                );
                                echo htmlspecialchars(isset($trangThaiHienThi[$row['trangThai']]) ? $trangThaiHienThi[$row['trangThai']] : $row['trangThai']);
                                ?>
                            </span>
                        </div>
                        
                        <div class="result-info">
                            <div class="info-row">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars(isset($row['hoTen']) ? $row['hoTen'] : ''); ?></span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-id-card"></i>
                                <span>CCCD: <?php echo htmlspecialchars(isset($row['CCCD']) ? $row['CCCD'] : ''); ?></span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars(isset($row['soDienThoai']) ? $row['soDienThoai'] : ''); ?></span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-calendar-check"></i>
                                <span>Nhận phòng: <?php echo isset($row['ngayNhanPhong']) ? date('d/m/Y', strtotime($row['ngayNhanPhong'])) : ''; ?></span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-calendar-times"></i>
                                <span>Trả phòng: <?php echo isset($row['ngayTraPhong']) ? date('d/m/Y', strtotime($row['ngayTraPhong'])) : ''; ?></span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-bed"></i>
                            <span>Số phòng: <?php echo htmlspecialchars($row['soPhong']); ?> phòng</span>
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
                <p>Không có giao dịch nào phù hợp với từ khóa tìm kiếm</p>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>


                <?php if (!empty($maDDPView)): ?>
                    <div class="detail-container">
                        <?php if ($chiTietGiaoDich && !empty($chiTietGiaoDich['donDatPhong'])): ?>
                    
                            <div class="alert alert-info alert-dismissible fade show mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Đang xem chi tiết đơn: <strong><?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['maDDP']); ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
       
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
                                            <div class="info-value info-value-highlight">
                                                <?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['maDDP']); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-calendar-plus"></i>Ngày đặt
                                            </div>
                                            <div class="info-value">
                                                <?php echo !empty($chiTietGiaoDich['donDatPhong']['ngayDatPhong']) ? 
                                                    date('d/m/Y', strtotime($chiTietGiaoDich['donDatPhong']['ngayDatPhong'])) : 'N/A'; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-calendar-check"></i>Ngày nhận
                                            </div>
                                            <div class="info-value">
                                                <?php echo !empty($chiTietGiaoDich['donDatPhong']['ngayNhanPhong']) ? 
                                                    date('d/m/Y', strtotime($chiTietGiaoDich['donDatPhong']['ngayNhanPhong'])) : 'N/A'; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-calendar-minus"></i>Ngày trả
                                            </div>
                                            <div class="info-value">
                                                <?php echo !empty($chiTietGiaoDich['donDatPhong']['ngayTraPhong']) ? 
                                                    date('d/m/Y', strtotime($chiTietGiaoDich['donDatPhong']['ngayTraPhong'])) : 'N/A'; ?>
                                            </div>
                                        </div>
                                        
                                        <?php 
                                        // Tính số ngày ở
                                        $soNgayO = 1;
                                        if (!empty($chiTietGiaoDich['donDatPhong']['ngayNhanPhong']) && 
                                            !empty($chiTietGiaoDich['donDatPhong']['ngayTraPhong'])) {
                                            $ngayNhan = strtotime($chiTietGiaoDich['donDatPhong']['ngayNhanPhong']);
                                            $ngayTra = strtotime($chiTietGiaoDich['donDatPhong']['ngayTraPhong']);
                                            $soNgayO = ($ngayTra - $ngayNhan) / (60 * 60 * 24);
                                            $soNgayO = max(1, ceil($soNgayO)); // Ít nhất 1 ngày, làm tròn lên
                                        }
                                        
                                        // Nếu có tính toán tiền phòng, lấy từ đó
                                        if (isset($chiTietGiaoDich['tienPhong']) && isset($chiTietGiaoDich['tienPhong']['soNgayO'])) {
                                            $soNgayO = $chiTietGiaoDich['tienPhong']['soNgayO'];
                                        }
                                        ?>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-moon"></i>Số ngày ở
                                            </div>
                                            <div class="info-value">
                                                <span class="fw-bold text-primary"><?php echo $soNgayO; ?> ngày</span>
                                                </div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-hashtag"></i>Số lượng phòng
                                            </div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars(isset($phong['soPhong']) ? $phong['soPhong'] : ''); ?> 
                                            </div>

                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-circle"></i>Trạng thái
                                            </div>
                                            <span class="status-badge status-confirmed">
                                                <?php 
                                                $trangThaiHienThi = array(
                                                    'DangCho' => 'Đang chờ',
                                                    'DaNhan' => 'Đã nhận phòng',
                                                    'DaTra' => 'Đã trả phòng',
                                                    'DaHuy' => 'Đã hủy'
                                                );
                                                echo htmlspecialchars(isset($trangThaiHienThi[$chiTietGiaoDich['donDatPhong']['trangThai']]) ? 
                                                    $trangThaiHienThi[$chiTietGiaoDich['donDatPhong']['trangThai']] : 
                                                    $chiTietGiaoDich['donDatPhong']['trangThai']);
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                       
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
                            
                            <?php if (!empty($chiTietGiaoDich['chiTietPhong'])): ?>
                                <div class="section-card">
                                    <h4 class="section-title">
                                        <i class="fas fa-bed me-2"></i>Chi tiết phòng đã đặt
                                        
                                        
                                    </h4>
                                    
                                    
                                    
                                    <div class="row">
                                        <?php 
                                        $stt = 1;
                                        $tongTienAll = 0;
                                        
                                        foreach ($chiTietGiaoDich['chiTietPhong'] as $phong): 
                                            // Tính tiền cho từng phòng
                                            $tienPhong = 0;
                                            $giaMotNgay = isset($phong['giaPhong']) ? $phong['giaPhong'] : 0;
                                            
                                            if (isset($chiTietGiaoDich['tienPhong']['chiTietTinh'])) {
                                                // Tìm thông tin tính toán cho phòng này
                                                foreach ($chiTietGiaoDich['tienPhong']['chiTietTinh'] as $tinh) {
                                                    if (isset($tinh['maPhong']) && $tinh['maPhong'] == $phong['maPhong']) {
                                                        $tienPhong = isset($tinh['tienPhong']) ? $tinh['tienPhong'] : 0;
                                                        $giaMotNgay = isset($tinh['giaMotNgay']) ? $tinh['giaMotNgay'] : 0;
                                                        break;
                                                    }
                                                }
                                            } else {
                                                // Tính đơn giản nếu không có chi tiết
                                                $tienPhong = $giaMotNgay * $soNgayO;
                                            }
                                            
                                            $tongTienAll += $tienPhong;
                                        ?>
                                            
                                            <?php $stt++; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <!-- Tổng kết tiền phòng -->
                                    <div class="mt-4 p-3 bg-light rounded">
                                        <h5><i class="fas fa-file-invoice-dollar me-2"></i>Tổng kết tiền phòng</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-2 ">
                                                    <i class="fas fa-bed me-2"></i>
                                                        Phòng <?php echo htmlspecialchars(isset($phong['soPhong']) ? $phong['soPhong'] : ''); ?></strong>
                                                        <span class="badge bg-primary">#<?php echo $stt; ?></span>
                                                    </div>
                                                <div class="mb-2">
                                                        <i class="fas fa-layer-group me-1"></i>
                                                        Tầng: <?php echo htmlspecialchars(isset($phong['tangPhong']) ? $phong['tangPhong'] : ''); ?>
                                                    </div>
                                                <div class="mb-2">
                                                    <i class="fas fa-bed me-2"></i>
                                                    <strong>Số phòng:</strong> <?php echo count($chiTietGiaoDich['chiTietPhong']); ?> phòng
                                                </div>
                                                <div class="mb-2">
                                                    <i class="fas fa-moon me-2"></i>
                                                    <strong>Số ngày ở:</strong> <?php echo $soNgayO; ?> ngày
                                                </div>
                                                <div class="mb-2">
                                                    <i class="fas fa-calendar-alt me-2"></i>
                                                    <strong>Thời gian:</strong> 
                                                    <?php echo date('d/m/Y', strtotime($chiTietGiaoDich['donDatPhong']['ngayNhanPhong'])); ?> 
                                                    đến 
                                                    <?php echo date('d/m/Y', strtotime($chiTietGiaoDich['donDatPhong']['ngayTraPhong'])); ?>
                                                </div>
                                                <div class="mt-2">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Trạng thái: 
                                                        <?php 
                                                        $trangThai = isset($phong['tinhTrang']) ? $phong['tinhTrang'] : '';
                                                        $badgeClass = 'bg-info';
                                                        if ($trangThai == 'Trong' || $trangThai == 'Trống') {
                                                            $badgeClass = 'bg-success';
                                                        } elseif ($trangThai == 'Đã đặt') {
                                                            $badgeClass = 'bg-warning';
                                                        } elseif ($trangThai == 'Đang ở' || $trangThai == 'Đang sử dụng') {
                                                            $badgeClass = 'bg-danger';
                                                        } elseif ($trangThai == 'Bảo trì') {
                                                            $badgeClass = 'bg-secondary';
                                                        }
                                                        ?>
                                                        <span class="badge <?php echo $badgeClass; ?>">
                                                            <?php echo htmlspecialchars($trangThai); ?>
                                                        </span>
                                                    </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="alert alert-success">
                                                    <h6 class="mb-1"><i class="fas fa-money-check-alt me-2"></i>Tổng tiền phòng:</h6>
                                                    <h4 class="text-success mb-0 fw-bold">
                                                        <?php echo number_format($tongTienAll, 0, ',', '.'); ?> VNĐ
                                                    </h4>
                                                    <small class="text-muted">
                                                        (<?php echo number_format(isset($giaMotNgay) ? $giaMotNgay : 0, 0, ',', '.'); ?> VNĐ/đêm × <?php echo count($chiTietGiaoDich['chiTietPhong']); ?> phòng × <?php echo $soNgayO; ?> đêm)
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="section-card">
                                <h4 class="section-title">
                                    <i class="fas fa-check-circle me-2"></i>Xác nhận nhận phòng
                                </h4>
                                
                                <?php 
                                $coTheNhanPhong = $controller->cKiemTraCoTheNhanPhong($maDDPView);
                                $trongThoiGian = $controller->cKiemTraThoiGian($maDDPView);
                                ?>
                                
                                <?php if ($coTheNhanPhong && $trongThoiGian): ?>
                                    <form method="POST" action="" onsubmit="return confirmXacNhan();">
                                        <input type="hidden" name="maDDP" value="<?php echo htmlspecialchars($chiTietGiaoDich['donDatPhong']['maDDP']); ?>">
                                        
                                        <button type="submit" name="btnXacNhanNhanPhong" class="btn btn-confirm">
                                            <i class="fas fa-check me-2"></i>XÁC NHẬN NHẬN PHÒNG
                                        </button>
                                        
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Lưu ý:</strong> Thao tác này sẽ cập nhật trạng thái đơn thành "Đã nhận phòng" và cập nhật trạng thái phòng.
                                        </div>
                                    </form>
                                <?php elseif (!$trongThoiGian): ?>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>KHÔNG THỂ NHẬN PHÒNG</strong><br>
                                        Đã quá hạn 120 phút so với giờ check-in đã đăng ký.
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>KHÔNG THỂ NHẬN PHÒNG</strong><br>
                                        Đơn đặt phòng không ở trạng thái "Đang chờ" hoặc đã được nhận phòng.
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <h4 class="text-danger">Không tìm thấy thông tin</h4>
                                <p class="text-muted">Giao dịch <strong><?php echo htmlspecialchars($maDDPView); ?></strong> không tồn tại hoặc không ở trạng thái "Đang chờ"</p>
                                <a href="nhanphong.php" class="btn btn-view mt-3">
                                    <i class="fas fa-arrow-left me-1"></i>Quay lại tìm kiếm
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function confirmXacNhan() {
            return confirm('BẠN CÓ CHẮC CHẮN MUỐN XÁC NHẬN NHẬN PHÒNG?\n\nĐơn đặt sẽ được cập nhật trạng thái "Đã nhận phòng".');
        }

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
        
        document.addEventListener('DOMContentLoaded', function() {
            var searchInput = document.querySelector('input[name="txtTimKiem"]');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }
        });
    </script>
</body>
</html>