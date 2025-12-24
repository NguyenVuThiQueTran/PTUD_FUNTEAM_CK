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
$hoTen = "";
if (isset($_SESSION["hoTen"]) && !empty($_SESSION["hoTen"])) {
    $hoTen = $_SESSION["hoTen"];
}

// Khai báo biến
$ketQuaTimKiem = array();
$chiTietDonDatPhong = null;
$tuKhoa = "";
$thongBao = "";
$maDDPView = "";
include_once("../controller/cHuyGiaoDich.php");

if (!class_exists('controlHuyGiaoDich')) {
    die("Lỗi: Không thể tải controller!");
}

$controller = new controlHuyGiaoDich();

if (isset($_POST["btnTimKiem"]) && isset($_POST["txtTimKiem"])) {
    $txtTimKiem = trim($_POST["txtTimKiem"]);
    if (!empty($txtTimKiem)) {
        $tuKhoa = $txtTimKiem;
        $ketQuaTimKiem = $controller->cTimKiemDonDatPhong($tuKhoa);
    }
}

if (isset($_GET["xemchitiet"]) && !empty($_GET["xemchitiet"])) {
    $maDDPView = trim($_GET["xemchitiet"]);
    $chiTietDonDatPhong = $controller->cLayChiTietDonDatPhong($maDDPView);
}

if (isset($_POST["btnHuyDonDatPhong"]) && isset($_POST["maDDP"])) {
    $maDDP = trim($_POST["maDDP"]);
    $controller->cHuyDonDatPhong($maDDP);
}
function toSafeString($str) {
    if (!mb_check_encoding($str, 'UTF-8')) {
        $str = mb_convert_encoding($str, 'UTF-8', 'auto');
    }
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// XỬ LÝ ALERT AN TOÀN
function safeAlert($message) {
    $message = mb_convert_encoding($message, 'UTF-8', 'auto');
    $message = str_replace(
        array('\\', "'", '"', "\n", "\r"),
        array('\\\\', "\\'", '\\"', '\\n', '\\r'),
        $message
    );
    return $message;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hủy Đơn Đặt Phòng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="/PTUD_FunTeam-main/css/huygiaodich.css">
</head>
<body>
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
            <!-- Header -->
            <div class="card-header-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3><i class="fas fa-calendar-times me-2"></i>Hủy Đơn Đặt Phòng</h3>
                        <p>Tìm kiếm và quản lý hủy đơn đặt phòng</p>
                    </div>
                    <a href="dashboard_letan.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- Form tìm kiếm -->
                <div class="search-container">
                    <form method="POST" action="">
                        <div class="search-box">
                            <input type="text" name="txtTimKiem" class="search-input" 
                                   placeholder="Nhập mã đơn, số điện thoại, CCCD hoặc tên khách hàng..." 
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
                                                <?php echo ($row['trangThai'] == 'Đã xác nhận') ? 'status-confirmed' : 
                                                       (($row['trangThai'] == 'DaHuy') ? 'status-cancelled' : 'status-pending'); ?>">
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
                                <i class="fas fa-search"></i>
                                <h4 class="mt-3">Không tìm thấy kết quả</h4>
                                <p>Không có đơn đặt phòng nào phù hợp với từ khóa tìm kiếm</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Hiển thị chi tiết đơn đặt phòng -->
                <?php if (!empty($maDDPView)): ?>
                    <div class="detail-container">
                        <?php if ($chiTietDonDatPhong && !empty($chiTietDonDatPhong['donDatPhong'])): ?>
                            <!-- Debug info -->
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Đang xem chi tiết đơn: <strong><?php echo htmlspecialchars($chiTietDonDatPhong['donDatPhong']['maDDP']); ?></strong>
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
                                            <div class="info-value info-value-highlight"><?php echo htmlspecialchars($chiTietDonDatPhong['donDatPhong']['maDDP']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-calendar-plus"></i>Ngày đặt
                                            </div>
                                            <div class="info-value"><?php echo date('d/m/Y', strtotime($chiTietDonDatPhong['donDatPhong']['ngayDatPhong'])); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-calendar-check"></i>Ngày nhận
                                            </div>
                                            <div class="info-value"><?php echo date('d/m/Y', strtotime($chiTietDonDatPhong['donDatPhong']['ngayNhanPhong'])); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-calendar-minus"></i>Ngày trả
                                            </div>
                                            <div class="info-value"><?php echo date('d/m/Y', strtotime($chiTietDonDatPhong['donDatPhong']['ngayTraPhong'])); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-hashtag"></i>Số lượng phòng
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietDonDatPhong['donDatPhong']['soLuong']); ?> phòng</div>
                                        </div>
                                        
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-circle"></i>Trạng thái
                                            </div>
                                            <span class="status-badge 
                                                <?php echo ($chiTietDonDatPhong['donDatPhong']['trangThai'] == 'Đã xác nhận') ? 'status-confirmed' : 
                                                       (($chiTietDonDatPhong['donDatPhong']['trangThai'] == 'DaHuy') ? 'status-cancelled' : 'status-pending'); ?>">
                                                <?php echo htmlspecialchars($chiTietDonDatPhong['donDatPhong']['trangThai']); ?>
                                            </span>
                                        </div>
                                        
                                        <?php if (!empty($chiTietDonDatPhong['donDatPhong']['ghiChu'])): ?>
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-sticky-note"></i>Ghi chú
                                                </div>
                                                <div class="info-value"><?php echo htmlspecialchars($chiTietDonDatPhong['donDatPhong']['ghiChu']); ?></div>
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
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietDonDatPhong['khachHang']['hoTen']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-envelope"></i>Email
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietDonDatPhong['khachHang']['email']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-phone"></i>Số điện thoại
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietDonDatPhong['khachHang']['soDienThoai']); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-id-card"></i>CCCD
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietDonDatPhong['khachHang']['CCCD']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-map-marker-alt"></i>Địa chỉ
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($chiTietDonDatPhong['khachHang']['diaChi']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="fas fa-crown"></i>Loại khách hàng
                                            </div>
                                            <div class="info-value">
                                                <?php echo ($chiTietDonDatPhong['khachHang']['loaiKH'] == 'VIP') ? 
                                                    '<span class="text-warning fw-bold">VIP</span>' : 'Thường'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (isset($chiTietDonDatPhong['hoaDon']) && $chiTietDonDatPhong['hoaDon']): ?>
                                <div class="section-card">
                                    <h4 class="section-title">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>Thông tin hóa đơn
                                    </h4>
                                    
                                    <div class="info-grid">
                                        <div>
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-file-invoice"></i>Mã hóa đơn
                                                </div>
                                                <div class="info-value info-value-highlight"><?php echo htmlspecialchars($chiTietDonDatPhong['hoaDon']['maHD']); ?></div>
                                            </div>
                                            
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-calendar-day"></i>Ngày lập
                                                </div>
                                                <div class="info-value"><?php echo date('d/m/Y', strtotime($chiTietDonDatPhong['hoaDon']['ngayLap'])); ?></div>
                                            </div>
                                            
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-money-bill-wave"></i>Tổng tiền
                                                </div>
                                                <div class="info-value money"><?php echo number_format($chiTietDonDatPhong['hoaDon']['tongTien'], 0, ',', '.'); ?> VNĐ</div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-credit-card"></i>Phương thức thanh toán
                                                </div>
                                                <div class="info-value">
                                                    <?php 
                                                    $phuongThuc = $chiTietDonDatPhong['hoaDon']['phuongThucTT'];
                                                    echo ($phuongThuc == 'Tiền mặt') ? 'Tiền mặt' : 
                                                         (($phuongThuc == 'Chuyển khoản') ? 'Chuyển khoản' : 'Thẻ tín dụng');
                                                    ?>
                                                </div>
                                            </div>
                                            
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-check-circle"></i>Trạng thái thanh toán
                                                </div>
                                                <div class="info-value">
                                                    <?php if ($chiTietDonDatPhong['hoaDon']['trangThai'] == 'Đã thanh toán'): ?>
                                                        <span class="badge bg-success">Đã thanh toán</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Chưa thanh toán</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                
                            <?php endif; ?>
                            
                    
                            <?php if (!empty($chiTietDonDatPhong['chiTietPhong'])): ?>
                               
                            <?php endif; ?>
                            
                  
                            <div class="section-card">
                                <h4 class="section-title">
                                    <i class="fas fa-ban me-2"></i>Hủy đơn đặt phòng
                                </h4>
                                
                                <?php 

                                $coTheHuy = $controller->cKiemTraCoTheHuy($chiTietDonDatPhong['donDatPhong']['maDDP']);
                                
                                $ngayNhanPhong = $chiTietDonDatPhong['donDatPhong']['ngayNhanPhong'];
                                $ngayHienTai = date('Y-m-d');
                                $quaHan = (strtotime($ngayNhanPhong) <= strtotime($ngayHienTai));
                                
                                $lyDoKhongTheHuy = '';
                                if ($chiTietDonDatPhong['donDatPhong']['trangThai'] == 'DaHuy') {
                                    $lyDoKhongTheHuy = 'Đơn đã bị hủy trước đó';
                                } elseif ($quaHan) {
                                    $lyDoKhongTheHuy = 'Đã đến/quá ngày nhận phòng (' . date('d/m/Y', strtotime($ngayNhanPhong)) . ')';
                                }
                                ?>
                                
                                <form method="POST" action="" onsubmit="return confirmHuyDon();">
                                    <input type="hidden" name="maDDP" value="<?php echo htmlspecialchars($chiTietDonDatPhong['donDatPhong']['maDDP']); ?>">
                                    
                                    <div class="d-flex align-items-center flex-wrap">
                                        <button type="submit" name="btnHuyDonDatPhong" 
                                                class="btn btn-cancel me-3 <?php echo $coTheHuy ? 'btn-cancel-danger' : 'btn-cancel-gray'; ?>"
                                                <?php echo !$coTheHuy ? 'disabled' : ''; ?>>
                                            <i class="fas fa-times-circle me-2"></i>HỦY ĐƠN ĐẶT PHÒNG
                                        </button>
                                        
                                        <?php if (!$coTheHuy): ?>
                                            <div class="cancel-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Không thể hủy:</strong> <?php echo $lyDoKhongTheHuy; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($coTheHuy): ?>
                                        
                                    <?php else: ?>
                                        <div class="alert alert-info mt-3 mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Thông tin:</strong> Đơn đặt phòng chỉ có thể hủy trước ngày nhận phòng.
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <h4 class="text-danger">Không tìm thấy thông tin chi tiết</h4>
                                <p class="text-muted">Đơn đặt phòng <strong><?php echo htmlspecialchars($maDDPView); ?></strong> không tồn tại hoặc đã bị xóa</p>
                                <a href="huygiaodich.php" class="btn btn-primary mt-3">
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

        function confirmHuyDon() {
            return confirm('BẠN CÓ CHẮC CHẮN MUỐN HỦY ĐƠN ĐẶT PHÒNG NÀY? "');
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