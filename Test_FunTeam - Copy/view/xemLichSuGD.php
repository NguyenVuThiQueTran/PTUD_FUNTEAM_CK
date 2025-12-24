<?php
// view/xemLichSuGD.php - Invoice History List
session_start();
header('Content-Type: text/html; charset=utf-8');

// Check login
if (!isset($_SESSION['idKH'])) {
    header('Location: login.php');
    exit();
}

// Include model
require_once("../model/clsLichSuGD.php");

// Get filter parameters
$tuNgay = isset($_GET['tuNgay']) ? $_GET['tuNgay'] : null;
$denNgay = isset($_GET['denNgay']) ? $_GET['denNgay'] : null;

// Get data filtered by logged-in customer
$model = new clsLichSuGD();
$idKH = intval($_SESSION['idKH']);
$danhSachHoaDon = $model->layLichSuGiaoDich($tuNgay, $denNgay, $idKH);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Giao Dịch</title>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="../BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            padding: 0;
        }
        
        /* Header */
        #header {
            width: 100%;
        }
        
        /* Main Layout - Same as thanhToan.php */
        .payment-layout {
            display: flex;
            min-height: calc(100vh - 100px);
            padding: 20px;
            gap: 20px;
        }
        
        /* Sidebar - Same as thanhToan.php */
        .sidebar {
            width: 280px;
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .sidebar-brand {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 8px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #666;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .sidebar-menu a i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .sidebar-menu a:hover {
            background: #f8f9fa;
            color: #667eea;
        }
        
        .sidebar-menu a.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .page-header {
            margin-bottom: 25px;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-title i {
            color: #667eea;
        }
        
        .page-subtitle {
            color: #999;
            font-size: 0.95rem;
        }
        
        /* Date Filter */
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .filter-input {
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .filter-input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-filter {
            padding: 10px 25px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        /* Invoice List - Simple cards like screenshot */
        .invoice-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .invoice-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .invoice-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .invoice-month-title {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            margin-bottom: 15px;
            margin-top: 10px;
        }
        
        .invoice-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .invoice-id {
            font-size: 14px;
            color: #666;
        }
        
        .invoice-id strong {
            color: #333;
            font-weight: 600;
        }
        
        .invoice-amount {
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }
        
        .invoice-info-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
        }
        
        .invoice-info-row i {
            color: #667eea;
        }
        
        .invoice-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .success-badge {
            background: #d4edda;
            color: #155724;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .btn-detail {
            padding: 8px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-detail:hover {
            background: #5568d3;
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }
        
        .empty-icon {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-title {
            font-size: 20px;
            font-weight: 600;
            color: #666;
            margin-bottom: 10px;
        }
        
        .empty-message {
            color: #999;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .payment-layout {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div id="header">
        <?php include('../layout/header_kh.php'); ?>
    </div>
    
    <!-- Main Layout -->
    <div class="payment-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-credit-card me-2"></i>Quản lý giao dịch
            </div>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="thanhToan.php">
                        <i class="fas fa-file-invoice"></i>
                        Danh sách thanh toán
                    </a>
                </li>
                <li>
                    <a href="xemLichSuGD.php" class="active">
                        <i class="fas fa-history"></i>
                        Lịch sử giao dịch
                    </a>
                </li>
                <li>
                    <a href="dashboard_khachhang.php">
                        <i class="fas fa-home"></i>
                        Quay lại trang chủ
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-history"></i>
                    Lịch Sử Giao Dịch
                </h1>
                <p class="page-subtitle">Danh sách các hóa đơn đã thanh toán</p>
            </div>
            
            <!-- Date Filter -->
            <div class="filter-section">
                <form method="GET" action="">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-calendar-alt"></i>
                                Từ ngày
                            </label>
                            <input type="date" name="tuNgay" class="filter-input" value="<?php echo $tuNgay ? htmlspecialchars($tuNgay) : ''; ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-calendar-alt"></i>
                                Đến ngày
                            </label>
                            <input type="date" name="denNgay" class="filter-input" value="<?php echo $denNgay ? htmlspecialchars($denNgay) : ''; ?>">
                        </div>
                        
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i>
                            Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Invoice List -->
            <?php if (count($danhSachHoaDon) > 0): ?>
                <div class="invoice-list">
                    <?php 
                    $currentMonth = '';
                    foreach ($danhSachHoaDon as $hoaDon):
                        $month = date('m Y', strtotime($hoaDon['ngayLap']));
                        if ($month != $currentMonth) {
                            $currentMonth = $month;
                            echo '<div class="invoice-month-title">tháng ' . date('m Y', strtotime($hoaDon['ngayLap'])) . '</div>';
                        }
                    ?>
                        <div class="invoice-item">
                            <div class="invoice-header-row">
                                <div class="invoice-id">
                                    Mã hóa đơn <strong><?php echo htmlspecialchars($hoaDon['maHD']); ?></strong>
                                </div>
                                <div class="invoice-amount">
                                    <?php echo number_format($hoaDon['tongTien'], 0, ',', '.'); ?> VND
                                </div>
                            </div>
                            
                            <div class="invoice-info-row">
                                <i class="fas fa-calendar"></i>
                                Ngày lập: <?php echo date('d/m/Y', strtotime($hoaDon['ngayLap'])); ?>
                            </div>
                            
                            <div class="invoice-footer">
                                <span class="success-badge">Giao dịch thành công</span>
                                <a href="chiTietHoaDon.php?maHD=<?php echo urlencode($hoaDon['maHD']); ?>" class="btn-detail">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div class="empty-title">Không có hóa đơn càn thanh toán</div>
                    <div class="empty-message">Tất cả hóa đơn đã được thanh toán hoặc chưa có đơn đặt phòng nào</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="../BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>