<?php
// layout/header.php


$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Tự động xác định đường dẫn cho logo
$current_file = $_SERVER['PHP_SELF'];
$is_in_view_folder = (strpos($current_file, '/view/') !== false);

if ($is_in_view_folder) {
    $logo_path = '../img/logos.jpg';
    $index_path = '../index.php';
    $login_path = 'view/login.php'; // Đã ở trong view/
    $register_path = 'dangky_khachhang.php'; // Đã ở trong view/
    $lienhe_path = 'view/lienhe.php';
    $khuyenmai_path = 'view/khuyenmai.php';
} else {
    $logo_path = 'img/logos.jpg';
    $index_path = 'index.php';
    $login_path = 'view/login.php';
    $register_path = 'view/dangky_khachhang.php';
    $lienhe_path = 'view/lienhe.php';
    $khuyenmai_path = 'view/khuyenmai.php';
}
?>
<div class="row">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #0d22ac, #1e3c72);">
        <div class="container-fluid">
            <div class="col-auto ps-4">
                <!-- SỬA Ở ĐÂY: Thay href="index.php" và src="img/logo.jpg" bằng biến -->
                <a class="navbar-brand" href="<?php echo $index_path; ?>">
                    <img src="<?php echo $logo_path; ?>" style="width:90px;" class="rounded-circle" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <div class="col-md-9">
                    <ul class="navbar-nav justify-content-center w-100" style="font-size: 1.0rem;">
                        <!-- SỬA Ở ĐÂY: Thay href="index.php" bằng biến -->
                        <li class="nav-item mx-2">
                            <a href="<?php echo $index_path; ?>" class="nav-link text-white">
                                <i class="fas fa-home me-1 fs-4 px-3"></i>Trang Chủ
                            </a>
                        </li>
                        <li class="nav-item mx-2">
                            <a href="#" class="nav-link text-white">
                                <i class="fas fa-concierge-bell me-1 fs-4 px-3"></i>Dịch Vụ
                            </a>
                        </li>
                        <li class="nav-item mx-2">
                            <a href="<?php echo $khuyenmai_path; ?>" class="nav-link text-white">
                                <i class="fas fa-gift me-1 fs-4 px-3"></i>Khuyến mãi
                            </a>
                        </li>
                        <li class="nav-item mx-2">
                            <a href="<?php echo $lienhe_path; ?>" class="nav-link text-white">
                                <i class="fas fa-phone me-1 fs-4 px-3"></i> Liên hệ
                            </a>
                        </li>
                        <li class="nav-item mx-2">
                            <a href="/Test_FunTeam - Copy/view/phong/TraCuuPhong.php" class="nav-link text-white">
                                <i class="fas fa-hotel me-1 fs-4 px-4"></i>Đặt phòng
                            </a>
                        </li>
                        
                    </ul>
                </div>

                <div class="col" style="padding-right:30px;">
                    <ul id="ul2" class="navbar-nav justify-content-end">
                        <?php if ($isLoggedIn): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white fs-5 fw-bold" href="#" id="userDropdown" 
                                data-bs-toggle="dropdown" style="font-size: 1.2rem;">
                                    <i class="fas fa-user pe-2 fs-5"></i><?php echo htmlspecialchars($username); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="?page=dashboard">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-user-cog me-2"></i>Tài khoản
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="?page=logout">
                                        <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                    </a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-white"><i class="fas fa-user pe-2"></i></span>
                                    <!-- SỬA Ở ĐÂY: Thay href="view/login.php" bằng biến -->
                                    <a href="<?php echo $login_path; ?>" class="text-white text-decoration-none">Đăng nhập |</a>
                                    <a href="<?php echo $register_path; ?>" class="text-white text-decoration-none">Đăng ký</a>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</div>