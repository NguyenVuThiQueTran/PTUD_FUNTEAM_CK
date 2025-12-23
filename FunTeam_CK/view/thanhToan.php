<?php
// view/thanhToan.php - Standalone file
session_start();
header('Content-Type: text/html; charset=utf-8');

// Check login
if (!isset($_SESSION['idKH'])) {
    header('Location: login.php');
    exit();
}

// Include model
require_once("../model/clsThanhToan.php");

// Get data filtered by logged-in customer
$model = new clsThanhToan();
$idKH = intval($_SESSION['idKH']);
$danhSachHoaDon = $model->layDanhSachHoaDonChuaThanhToan($idKH);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Hóa Đơn</title>
    
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
        
        .container-fluid {
            padding: 0;
        }
        
        /* Header */
        #header {
            width: 100%;
        }
        
        /* Main Layout */
        .payment-layout {
            display: flex;
            min-height: calc(100vh - 100px);
            padding: 20px;
            gap: 20px;
        }
        
        /* Sidebar */
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
        
        /* Invoice Cards */
        .invoices-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }
        
        .invoice-card {
            background: white;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .invoice-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
            transform: translateY(-3px);
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .invoice-id {
            font-size: 1.1rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .invoice-status {
            background: #fff3cd;
            color: #856404;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .invoice-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        
        .invoice-label {
            color: #999;
        }
        
        .invoice-value {
            font-weight: 600;
            color: #333;
        }
        
        .invoice-total {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: right;
            margin-top: 15px;
        }
        
        .invoice-total-label {
            font-size: 0.9rem;
            margin-bottom: 5px;
            opacity: 0.9;
        }
        
        .invoice-total-amount {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .btn-pay {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
        }
        
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-icon {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #666;
            margin-bottom: 10px;
        }
        
        .empty-message {
            color: #999;
            font-size: 0.95rem;
        }
        
        .empty-action {
            margin-top: 25px;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            color: white;
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
                    <a href="thanhToan.php" class="active">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Danh sách thanh toán
                    </a>
                </li>
                <li>
                    <a href="xemLichSuGD.php">
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
                    <i class="fas fa-receipt"></i>
                    Thanh Toán Hóa Đơn
                </h1>
                <p class="page-subtitle">Danh sách các hóa đơn chưa thanh toán</p>
            </div>
            
            <?php if (count($danhSachHoaDon) > 0): ?>
                <div class="invoices-grid">
                    <?php foreach ($danhSachHoaDon as $hoaDon): ?>
                        <div class="invoice-card">
                            <div class="invoice-header">
                                <div class="invoice-id"><?php echo htmlspecialchars($hoaDon['maHD']); ?></div>
                                <div class="invoice-status">Chưa thanh toán</div>
                            </div>
                            
                            <div class="invoice-detail">
                                <span class="invoice-label">Ngày lập:</span>
                                <span class="invoice-value"><?php echo date('d/m/Y', strtotime($hoaDon['ngayLap'])); ?></span>
                            </div>
                            
                            <div class="invoice-detail">
                                <span class="invoice-label">Mã đặt phòng:</span>
                                <span class="invoice-value"><?php echo htmlspecialchars($hoaDon['maDDP']); ?></span>
                            </div>
                            
                            <div class="invoice-total">
                                <div class="invoice-total-label">Tổng tiền</div>
                                <div class="invoice-total-amount">
                                    <?php echo number_format($hoaDon['tongTien'], 0, ',', '.'); ?>đ
                                </div>
                            </div>
                            
                            <button class="btn-pay" onclick="window.location.href='chiTietThanhToan.php?maHD=<?php echo urlencode($hoaDon['maHD']); ?>'">
                                <i class="fas fa-credit-card me-2"></i>Thanh toán ngay
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-smile"></i>
                    </div>
                    <h3 class="empty-title">Không có hóa đơn cần thanh toán</h3>
                    <p class="empty-message">Bạn đã thanh toán tất cả các hóa đơn!</p>
                    <div class="empty-action">
                        <a href="dashboard_khachhang.php" class="btn-primary-custom">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại trang chủ
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>