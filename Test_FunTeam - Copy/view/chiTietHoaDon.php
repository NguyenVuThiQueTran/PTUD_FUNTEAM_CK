<?php
// view/chiTietHoaDon.php - Invoice Detail Page
session_start();
header('Content-Type: text/html; charset=utf-8');

// Check login
if (!isset($_SESSION['idKH'])) {
    header('Location: login.php');
    exit();
}

// Include model
require_once("../model/clsLichSuGD.php");

// Get invoice ID
$maHD = isset($_GET['maHD']) ? $_GET['maHD'] : null;

if (!$maHD) {
    echo "<script>alert('Mã hóa đơn không hợp lệ!'); window.location.href='xemLichSuGD.php';</script>";
    exit();
}

// Get data
$model = new clsLichSuGD();
$hoaDon = $model->layChiTietHoaDon($maHD);

if (!$hoaDon) {
    echo "<script>alert('Không tìm thấy hóa đơn!'); window.location.href='xemLichSuGD.php';</script>";
    exit();
}

// Get customer name from database
require_once("../model/clsconnect.php");
$db = new clsKetNoi();
$conn = $db->moketnoi();
$sqlKH = "SELECT k.hoTen FROM khachhang k 
          INNER JOIN dondatphong d ON k.idKH = d.idKH 
          WHERE d.maDDP = '" . $conn->real_escape_string($hoaDon['maDDP']) . "'";
$resultKH = $conn->query($sqlKH);
$tenKH = 'Khách hàng';
if ($resultKH && $resultKH->num_rows > 0) {
    $rowKH = $resultKH->fetch_assoc();
    $tenKH = $rowKH['hoTen'];
}
$db->dongketnoi();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đặt chỗ - <?php echo htmlspecialchars($hoaDon['maHD']); ?></title>
    
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
        
        #header {
            width: 100%;
        }
        
        .page-container {
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .detail-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 25px 30px;
        }
        
        .card-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .card-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title i {
            color: #667eea;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #333;
        }
        
        /* Service Table */
        .service-table {
            width: 100%;
            border-collapse: collapse;
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .service-table thead {
            background: #667eea;
            color: white;
        }
        
        .service-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }
        
        .service-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
            color: #333;
            background: white;
        }
        
        .service-table tr:last-child td {
            border-bottom: none;
        }
        
        .service-table tbody tr:hover {
            background: #f8f9fa !important;
        }
        
        .btn-back {
            padding: 12px 25px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div id="header">
        <?php include('../layout/header_kh.php'); ?>
    </div>
    
    <div class="page-container">
        <div class="container">
        <!-- Back Button -->
        <div style="margin-bottom: 20px;">
            <a href="xemLichSuGD.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Quay lại
            </a>
        </div>
        
        <!-- Detail Card -->
        <div class="detail-card">
            <div class="card-header">
                <div class="card-title">Chi tiết đặt chỗ</div>
                <div class="card-subtitle">Mã hóa đơn: <?php echo htmlspecialchars($hoaDon['maHD']); ?></div>
            </div>
            
            <div class="card-body">
                <!-- Booking Info Section -->
                <div class="section">
                    <div class="section-title">
                        <i class="fas fa-user"></i>
                        Thông tin đặt chỗ
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Người đặt</span>
                            <span class="info-value"><?php echo htmlspecialchars($tenKH); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ngày đặt</span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($hoaDon['ngayLap'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Mã đặt phòng</span>
                            <span class="info-value"><?php echo htmlspecialchars($hoaDon['maDDP']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phương thức thanh toán</span>
                            <span class="info-value"><?php echo htmlspecialchars($hoaDon['phuongThucThanhToan']); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Service Details Section -->
                <?php if (isset($hoaDon['dichVu']) && count($hoaDon['dichVu']) > 0): ?>
                    <div class="section">
                        <div class="section-title">
                            <i class="fas fa-concierge-bell"></i>
                            Thông tin dịch vụ
                        </div>
                        <table class="service-table">
                            <thead>
                                <tr>
                                    <th>Tên dịch vụ đã đăng ký</th>
                                    <th>Số lượng</th>
                                    <th>Tên phòng đã đặt</th>
                                    <th>Hạng phòng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($hoaDon['dichVu'] as $dv): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($dv['tenDV']); ?></td>
                                        <td><?php echo $dv['soLuong']; ?></td>
                                        <td><?php echo isset($hoaDon['phong']) && $hoaDon['phong'] ? htmlspecialchars($hoaDon['phong']['maPhong']) : 'Chưa có'; ?></td>
                                        <td><?php echo isset($hoaDon['phong']) && $hoaDon['phong'] ? htmlspecialchars($hoaDon['phong']['hangPhong']) : 'Chưa có'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="../BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>