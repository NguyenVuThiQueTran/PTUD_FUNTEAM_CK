<?php
// view/dashboard_letan.php

// Bắt đầu session
session_start();

// **SỬA: Kiểm tra đăng nhập TRƯỚC khi xử lý**
if (!isset($_SESSION["dn"]) || $_SESSION["dn"] !== true || $_SESSION["role"] !== 'buongphong') {
    header("Location: ../index.php");
    exit();
}

// **SỬA: Đặt biến session cho header nhận diện - ĐẶT SAU KIỂM TRA ĐĂNG NHẬP**
$_SESSION['user_role'] = 'buongphong';
$_SESSION['current_dashboard'] = 'dashboard_buongphong.php';

// Định nghĩa đường dẫn
$base_path = dirname(__DIR__); // Thư mục gốc của project
$index_path = '../index.php'; // Trỏ về index.php từ thư mục view
$dashboard_path = 'dashboard_buongphong.php'; // Tên file dashboard hiện tại

// Lấy thông tin từ session
$username = isset($_SESSION["username"]) && !empty($_SESSION["username"]) ? $_SESSION["username"] : "Nhân viên buồng phòng";
$email = isset($_SESSION["email"]) && !empty($_SESSION["email"]) ? $_SESSION["email"] : "";
$maNS = isset($_SESSION["maNS"]) && !empty($_SESSION["maNS"]) ? $_SESSION["maNS"] : "";

// Lấy dữ liệu thống kê cho lễ tân
require_once '../model/clslogin.php';
$p = new nodeUser();

// Lấy dữ liệu thống kê
$stats = $p->mGetDashboardData('buongphong', isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null);

// Lấy các dữ liệu khác
$customers = $p->mGetAllCustomers();
$availableRooms = $p->mGetAvailableRooms();
$stayingGuests = $p->mGetStayingGuests();
$recentBookings = $p->mGetRecentBookings();
$todayCheckins = $p->mGetTodayCheckins();
$todayCheckouts = $p->mGetTodayCheckouts();
$roomStats = $p->mGetRoomStatistics();

$pageTitle = "Dashboard - NV buồng phòng";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../img/logo.jpg">
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/shared.css">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="../BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #ff6b6b;
        }
        
        body { 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .container-fluid {
            padding: 0;
            margin: 0;
        }
        
        /* Header */
        #header {
            width: 100%;
        }
        
        /* Banner */
        .banner-container {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        .banner-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            display: block;
        }
        
        /* Footer đụng banner */
        #footer {
            margin-top: 0;
            width: 100%;
        }
        
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        
        <div id="header" >
            <?php include('../layout/header_bt.php'); ?>
        </div>
        
        <!-- Banner Section -->
        <div class="row">
            <div class="col-12 banner-container">
                <img src="../img/Nen.jpg" alt="Banner" class="banner-image">
                
            </div>
        </div>
  
    <!-- Footer -->
        <div id="footer">
            <?php include('../layout/footer.php'); ?>
        </div>

</div>


</body>
</html>