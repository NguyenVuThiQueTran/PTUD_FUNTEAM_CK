<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Khách Sạn - Admin Dashboard</title>
    
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    
    <style>
        .bg-funteam { background-color: #102a83 !important; }
        .navbar-nav .nav-link {
            display: flex !important; flex-direction: column !important; align-items: center !important;
            color: rgba(255,255,255,0.9) !important; font-size: 0.9rem; font-weight: 500;
            white-space: nowrap !important; min-width: 100px; padding: 8px 10px !important;
            border-radius: 8px; transition: all 0.2s ease;
        }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
            color: #fff !important; background-color: rgba(255, 255, 255, 0.2);
        }
        .nav-icon { font-size: 1.4rem; margin-bottom: 5px; }
        .navbar-brand img { background: white; padding: 2px; border-radius: 50%; height: 50px; width: 50px; object-fit: contain; }
        .user-section { border-left: 1px solid rgba(255,255,255,0.3); padding-left: 15px; margin-left: 10px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-funteam shadow">
    <div class="container-fluid px-3">
        <a class="navbar-brand d-flex align-items-center me-4" href="dashboard.php">
            <img src="img/logos.jpg" alt="Logo" class="me-2">
            <div class="d-none d-xl-block text-white" style="line-height: 1.2;">
                <span class="fw-bold text-uppercase d-block" style="font-size: 1.2rem;">Funteam</span>
                <small style="font-size: 0.75rem; opacity: 0.8;">Hotel Management</small>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu"><span class="navbar-toggler-icon"></span></button>

        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?php echo (!isset($_GET['page'])||$_GET['page']=='home')?'active':''; ?>" href="dashboard.php"><i class="fas fa-home nav-icon"></i><span>Trang Chủ</span></a></li>
                <li class="nav-item"><a class="nav-link <?php echo (isset($_GET['page'])&&$_GET['page']=='phong')?'active':''; ?>" href="dashboard.php?page=phong"><i class="fas fa-bed nav-icon"></i><span>Quản lý Phòng</span></a></li>
                <li class="nav-item"><a class="nav-link <?php echo (isset($_GET['page'])&&$_GET['page']=='khachhang')?'active':''; ?>" href="dashboard.php?page=khachhang"><i class="fas fa-users nav-icon"></i><span>Khách Hàng</span></a></li>
                <li class="nav-item"><a class="nav-link <?php echo (isset($_GET['page'])&&$_GET['page']=='dichvu')?'active':''; ?>" href="dashboard.php?page=dichvu"><i class="fas fa-concierge-bell nav-icon"></i><span>Dịch Vụ</span></a></li>
                <li class="nav-item"><a class="nav-link <?php echo (isset($_GET['page'])&&$_GET['page']=='nhanvien')?'active':''; ?>" href="dashboard.php?page=nhanvien"><i class="fas fa-user-tie nav-icon"></i><span>Nhân Sự</span></a></li>
            </ul>
            <div class="d-flex align-items-center mt-3 mt-lg-0 user-section">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown">
                        <div class="text-end me-2 d-none d-md-block">
                            <small class="d-block text-white-50" style="font-size: 0.7rem;">Xin chào,</small>
                            <span class="fw-bold"><?php echo isset($_SESSION['user']['hoTen']) ? $_SESSION['user']['hoTen'] : "Admin"; ?></span>
                        </div>
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-user"></i></div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<div class="container-fluid mt-4">