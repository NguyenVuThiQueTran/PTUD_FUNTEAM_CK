<?php
// layout/headers.php
// **THÊM:** Bắt đầu session
if (!isset($_SESSION)) {
    session_start();
}

// Kiểm tra đăng nhập
$isLoggedIn = isset($_SESSION["dn"]) && $_SESSION["dn"] === true;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// **SỬA:** Tự động xác định đường dẫn cho logo
$current_file = $_SERVER['PHP_SELF'];
$is_in_view_folder = (strpos($current_file, '/view/') !== false);

if ($is_in_view_folder) {
    $logo_path = '../img/logos.jpg';
    $index_path = '../index.php';
    $login_path = 'login.php';
    $register_path = 'dangky_khachhang.php';
} else {
    $logo_path = 'img/logos.jpg';
    $index_path = 'index.php';
    $login_path = 'view/login.php';
    $register_path = 'view/dangky_khachhang.php';
}

// **SỬA ĐƠN GIẢN: Xác định trang chủ dựa trên role**
if ($isLoggedIn && !empty($userRole)) {
    // Nếu đã đăng nhập
    switch($userRole) {
        case 'letan':
            $dashboard_file = 'dashboard_letan.php';
            break;
        case 'admin':
            $dashboard_file = 'dashboard_admin.php';
            break;
        case 'quanly':
            $dashboard_file = 'dashboard_quanly.php';
            break;
        case 'ketoan':
            $dashboard_file = 'dashboard_ketoan.php';
            break;
        case 'buongphong':
            $dashboard_file = 'dashboard_buongphong.php';
            break;
        case 'doan':
            $dashboard_file = 'dashboard_doan.php';
            break;
        case 'khachhang':
            $dashboard_file = 'dashboard_khachhang.php';
            break;
        default:
            $dashboard_file = 'dashboard.php';
    }
    
    // **SỬA QUAN TRỌNG:** Dashboard nằm trong thư mục view/
    $home_path = 'view/' . $dashboard_file;
    
    // Nếu đang ở trong view folder, cần thêm ../
    if ($is_in_view_folder) {
        $home_path = '../' . $home_path;
    }
    
    // Logo cũng trỏ về dashboard
    $logo_href = $home_path;
} else {
    // Chưa đăng nhập thì về index
    $home_path = $index_path;
    $logo_href = $index_path;
}

// **SỬA:** Đảm bảo không có đường dẫn trùng lặp
$logo_href = str_replace('//', '/', $logo_href);
$home_path = str_replace('//', '/', $home_path);

if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    // Xóa tất cả session
    $_SESSION = array();
    session_destroy();
    
    // Chuyển hướng
    header("Location: " . $index_path);
    exit();
}
?>
<style>
/* Hiệu ứng hover chỉ đổi màu chữ */
.nav-menu-item {
    transition: all 0.3s ease;
    border-radius: 5px;
    padding: 5px 10px !important;
    font-size: 0.85rem !important;
    font-weight: 500 !important;
}

.nav-menu-item:hover {
    color: gold !important;
}

.nav-menu-item:hover i {
    color: gold !important;
}

/* QUAN TRỌNG: CSS cho dropdown hiện khi hover */
.nav-item.dropdown:hover .dropdown-menu {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
}

/* Style dropdown menu */
.dropdown-menu {
    display: none;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    transform: translateY(10px);
    border: 1px solid rgba(255,255,255,0.2);
    background-color: rgba(30, 60, 114, 0.95) !important; /* Màu nền đậm */
    backdrop-filter: blur(10px);
    margin-top: 5px !important;
    min-width: 180px;
}

/* Style dropdown item */
.dropdown-item {
    color: white !important;
    padding: 10px 15px !important;
    transition: all 0.2s ease;
    border-radius: 3px;
    margin: 2px 5px;
    width: auto;
}

.dropdown-item:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: gold !important;
}

.dropdown-divider {
    border-color: rgba(255, 255, 255, 0.2) !important;
    margin: 5px 0 !important;
}

/* Để dropdown hiện ngay lập tức khi hover */
.nav-item.dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    right: 0;
    left: auto;
}

/* Hiệu ứng cho tên user khi hover */
#userDropdown:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
}
</style>
<div class="row">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #0d22ac, #1e3c72);">
        <div class="container-fluid">
            <div class="col-auto ps-4">
                <!-- Logo trỏ về dashboard nếu đã đăng nhập -->
                <a class="navbar-brand" href="<?php echo htmlspecialchars($logo_href); ?>">
                    <img src="<?php echo htmlspecialchars($logo_path); ?>" style="width:90px;" class="rounded-circle" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <div class="col-md-10 mx-2">
                    <ul class="navbar-nav justify-content-center w-100">
                        <!-- Trang Chủ trỏ về dashboard -->
                        
                            <li class="nav-item mx-2 my-3">
                        <a href="#" class="nav-link text-white nav-menu-item">
                            <i class="fas fa-bed me-1 fs-4 px-2"></i>Quản lý Phòng 
                        </a>
                    </li>
                    <li class="nav-item mx-2 my-3">
                        <a href="#" class="nav-link text-white nav-menu-item">
                            <i class="fas fa-concierge-bell me-1 fs-4 px-2"></i>Quản lý dịch vụ
                        </a>
                    </li>
                    <li class="nav-item mx-2 my-3">
                        <a href="khuyenmai/QuanLyKhuyenMai.php" class="nav-link text-white nav-menu-item">
                            <i class="fas fa-percentage me-1 fs-4 px-2"></i>Quản lý khuyến mãi
                        </a>
                    </li>
                    <li class="nav-item mx-2 my-3">
                        <a href="#" class="nav-link text-white nav-menu-item">
                            <i class="fas fa-users me-1 fs-4 px-2"></i>Quản lý khách hàng
                        </a>
                    </li>
                    <li class="nav-item mx-2 my-3">
                        <a href="#" class="nav-link text-white nav-menu-item">
                            <i class="fas fa-user-tie me-1 fs-4 px-2"></i>Quản lý nhân sự
                        </a>
                    </li>
                    <li class="nav-item mx-2 my-3">
                        <a href="baocao/XemBaoCao.php" class="nav-link text-white nav-menu-item">
                            <i class="fas fa-chart-line me-1 fs-4 px-2"></i>Báo cáo doanh thu
                        </a>
                    </li>
                    </ul>
                </div>

                <div class="col" style="padding-right:30px;">
    <ul id="ul2" class="navbar-nav justify-content-end">
        <?php if ($isLoggedIn): ?>
            <!-- ĐƠN GIẢN: Bỏ data-bs-toggle vì không cần click -->
            <li class="nav-item dropdown" onmouseover="showDropdown(this)" onmouseout="hideDropdown(this)">
                <a class="nav-link dropdown-toggle text-white fs-6 fw-bold" href="#" id="userDropdown" 
                                data-bs-toggle="dropdown" style="font-size: 0.8rem;">
                                    <i class="fas fa-user pe-2 fs-5"></i><?php echo htmlspecialchars($username); ?>
                                </a>
                <ul class="dropdown-menu" id="dropdownMenu">
                    <li><hr class="dropdown-divider m-0"></li>
                    <li>
                        <a class="dropdown-item text-white fw-bold" 
                           href="?logout=true" 
                           style="color: #ff6b6b !important;"
                           onmouseover="this.style.backgroundColor='rgba(255,107,107,0.2)'"
                           onmouseout="this.style.backgroundColor='transparent'">
                            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                        </a>
                    </li>
                </ul>
            </li>
            
            <script>
            // Hàm hiển thị dropdown khi hover
            function showDropdown(element) {
                var menu = element.querySelector('.dropdown-menu');
                if (menu) {
                    menu.style.display = 'block';
                    menu.style.opacity = '1';
                    menu.style.visibility = 'visible';
                    menu.style.transform = 'translateY(0)';
                }
            }
            
            // Hàm ẩn dropdown khi rời chuột
            function hideDropdown(element) {
                var menu = element.querySelector('.dropdown-menu');
                if (menu) {
                    // Delay một chút để người dùng có thể di chuột vào menu
                    setTimeout(function() {
                        if (!menu.matches(':hover') && !element.matches(':hover')) {
                            menu.style.display = 'none';
                            menu.style.opacity = '0';
                            menu.style.visibility = 'hidden';
                            menu.style.transform = 'translateY(10px)';
                        }
                    }, 200);
                }
            }
            
            // Đảm bảo menu cũng có thể hover
            document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                menu.addEventListener('mouseover', function() {
                    this.style.display = 'block';
                    this.style.opacity = '1';
                    this.style.visibility = 'visible';
                });
                
                menu.addEventListener('mouseout', function() {
                    setTimeout(function() {
                        if (!menu.matches(':hover')) {
                            menu.style.display = 'none';
                            menu.style.opacity = '0';
                            menu.style.visibility = 'hidden';
                        }
                    }, 300);
                });
            });
            </script>
        <?php else: ?>
            <li class="nav-item">
                <div class="nav-link">
                    <span class="text-white"><i class="fas fa-user pe-2"></i></span>
                    <a href="<?php echo htmlspecialchars($login_path); ?>" class="text-white text-decoration-none">Đăng nhập |</a>
                    <a href="<?php echo htmlspecialchars($register_path); ?>" class="text-white text-decoration-none">Đăng ký</a>
                </div>
            </li>
        <?php endif; ?>
    </ul>
</div>
            </div>
        </div>
    </nav>
</div>