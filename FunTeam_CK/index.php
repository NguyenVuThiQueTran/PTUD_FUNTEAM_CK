<?php
// PTUD_FunTeam-main/index.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Đường dẫn base
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/PTUD_FunTeam-main/';

// Kiểm tra đăng nhập
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user']);
$userInfo = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Xử lý logout
if (isset($_GET['page']) && $_GET['page'] == 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Dữ liệu khuyến mãi - DÙNG array() thay vì []
$promotions = array(
    array(
        'id' => 1,
        'title' => 'Ưu đãi đặt phòng sớm',
        'discount' => '20%',
        'code' => 'EARLY20',
        'description' => 'Nhận ngay 20% giảm giá khi đặt phòng trước',
        'valid_until' => '31/12/2025',
        'icon' => 'fa-calendar-check',
        'color' => 'primary'
    ),
    array(
        'id' => 2,
        'title' => 'Gói nghỉ dưỡng gia đình',
        'discount' => '15%',
        'code' => 'FAMILY15',
        'description' => 'Ưu đãi đặc biệt cho gia đình từ 3 người trở lên',
        'valid_until' => '15/06/2025',
        'icon' => 'fa-users',
        'color' => 'success'
    ),
    array(
        'id' => 3,
        'title' => 'Tuần lễ vàng',
        'discount' => '25%',
        'code' => 'GOLDEN25',
        'description' => 'Khuyến mãi đặc biệt trong tuần lễ vàng',
        'valid_until' => '19/12/2025',
        'icon' => 'fa-gem',
        'color' => 'warning'
    ),
    array(
        'id' => 4,
        'title' => 'Giảm giá cuối tuần',
        'discount' => '30%',
        'code' => 'WEEKEND30',
        'description' => 'Ưu đãi hấp dẫn cho các đặt phòng cuối tuần',
        'valid_until' => '20/12/2025',
        'icon' => 'fa-umbrella-beach',
        'color' => 'info'
    ),
    array(
        'id' => 5,
        'title' => 'Ưu đãi thành viên',
        'discount' => '10%',
        'code' => 'MEMBER10',
        'description' => 'Giảm giá thêm cho thành viên tích điểm',
        'valid_until' => '31/12/2025',
        'icon' => 'fa-crown',
        'color' => 'danger'
    ),
    array(
        'id' => 6,
        'title' => 'Combo ăn uống',
        'discount' => '40%',
        'code' => 'DINING40',
        'description' => 'Giảm 40% cho các gói ăn uống tại nhà hàng',
        'valid_until' => '1/1/2026',
        'icon' => 'fa-utensils',
        'color' => 'secondary'
    )
);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Hệ thống Du lịch</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="img/logo.jpg">
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/shared.css">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    
    <style>
        /* CSS cho banner với các chức năng */
        .banner-container {
            position: relative;
            width: 100%;
        }
        
        .banner-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
        }
        
        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 20px;
        }
        
        .banner-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .banner-subtitle {
            font-size: 1.2rem;
            margin-bottom: 15px;
            max-width: 600px;
        }
        
        .functions-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            max-width: 900px;
            margin-top: 50px;
        }
        
        .function-item {
            background-color: rgba(255, 255, 255, 0.9);
            color: #333;
            border-radius: 10px;
            padding: 15px 20px;
            min-width: 150px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
        }
        
        .function-item:hover {
            background-color: white;
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            color: #333;
        }
        
        .function-icon {
            font-size: 24px;
            margin-bottom: 8px;
            color: #1e3c72;
        }
        
        .function-name {
            font-weight: bold;
            font-size: 14px;
        }
        
        .search-box {
            background-color: white;
            border-radius: 50px;
            padding: 10px 25px;
            display: flex;
            align-items: center;
            width: 30%;
            min-width: 200px;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .search-box input {
            border: none;
            outline: none;
            flex: 1;
            padding: 10px 0;
            font-size: 16px;
        }
        
        .search-box button {
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .search-box button:hover {
            background-color: #ff5252;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-top: 30px;
            border-radius: 10px;
            text-align: center;
        }
        
        .welcome-section h2 {
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .quick-access {
            margin: 40px 0;
        }
        
        .quick-access-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            height: 100%;
            border: 2px solid transparent;
        }
        
        .quick-access-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            border-color: #667eea;
            text-decoration: none;
            color: #333;
        }
        
        .quick-access-btn i {
            font-size: 36px;
            margin-bottom: 15px;
            color: #667eea;
        }
        
        .quick-access-btn span {
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
        }
        
        .stats-section {
            background-color: #f8f9fa;
            padding: 40px 0;
            margin: 30px 0;
            border-radius: 10px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
        }
        
        @media (max-width: 768px) {
            .functions-container {
                flex-direction: column;
                align-items: center;
            }
            
            .function-item {
                width: 80%;
                min-width: unset;
            }
            
            .banner-title {
                font-size: 2rem;
            }
            
            .banner-subtitle {
                font-size: 1rem;
            }
            
            .search-box {
                width: 80%;
            }
        }
        
        /* Container cho các khuyến mãi */
        .promo-section {
            margin: 30px 0;
            padding: 20px 0;
        }
        
        .promo-section-title {
            color: #0d22ac;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        /* CARD khuyến mãi nhỏ */
        .promo-card-small {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #eaeaea;
            margin-bottom: 20px;
        }
        
        .promo-card-small:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        /* Header nhỏ */
        .promo-header-small {
            background: linear-gradient(135deg, #0d22ac, #1e3c72);
            color: white;
            padding: 15px;
            text-align: center;
            position: relative;
        }
        
        .promo-discount-small {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 5px;
        }
        
        .promo-code-small {
            background: rgba(255,255,255,0.15);
            border: 1px dashed rgba(255,255,255,0.5);
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
            margin: 8px 0;
            cursor: pointer;
            font-family: monospace;
        }
        
        /* Body nhỏ */
        .promo-body-small {
            padding: 15px;
        }
        
        .promo-title-small {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            height: 40px;
            overflow: hidden;
        }
        
        .promo-desc-small {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 12px;
            height: 40px;
            overflow: hidden;
        }
        
        .promo-footer-small {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        
        .promo-date-small {
            font-size: 0.8rem;
            color: #888;
        }
        
        .promo-btn-small {
            padding: 5px 12px;
            font-size: 0.8rem;
            border-radius: 4px;
        }
        
        /* Badge nhỏ */
        .promo-badge-small {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff6b6b;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        /* Xóa CSS khuyến mãi cũ không cần thiết */
        
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div id="header">
            <?php include('layout/header.php'); ?>
        </div>
        
        <!-- Banner Section -->
        <div class="row">
            <div class="col-12 banner-container">
                <img src="img/Nen.jpg" alt="Banner" class="banner-image">
                <div class="banner-overlay">
                    <?php if ($isLoggedIn): ?>
                        <h1 class="banner-title">Chào mừng <?php echo htmlspecialchars($username); ?>!</h1>
                        <p class="banner-subtitle">Hệ thống quản lý du lịch sẵn sàng phục vụ bạn</p>
                    <?php else: ?>
                        <h1 class="banner-title">Khám phá thế giới</h1>
                        <p class="banner-subtitle">Tìm kiếm và đặt các dịch vụ du lịch tốt nhất với giá cả hợp lý</p>
                    <?php endif; ?>
                    
                    <div class="search-box">
                        <input type="text" placeholder="Tìm kiếm điểm đến, khách sạn, hoạt động...">
                        <button>Tìm kiếm</button>
                    </div>
                    
                    <div class="functions-container">
                        <?php if ($isLoggedIn): ?>
                            
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
       
        <!-- Khuyến mãi Section -->
        <div class="promo-section">
            <h3 class="promo-section-title">
                <i class="fas fa-gift me-2"></i>Khuyến Mãi Hấp Dẫn
            </h3>
            
            <div class="row g-3" id="promotionsGrid">
                <?php foreach ($promotions as $promo): ?>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="promo-card-small">
                        <?php if (strpos($promo['discount'], '25') !== false || strpos($promo['discount'], '30') !== false || strpos($promo['discount'], '40') !== false): ?>
                        
                        <?php endif; ?>
                        
                        <div class="promo-header-small" style="background: linear-gradient(135deg, var(--bs-<?php echo $promo['color']; ?>), #1e3c72)">
                            <div class="promo-discount-small"><?php echo $promo['discount']; ?></div>
                            <div class="promo-code-small" onclick="copyPromoCode('<?php echo $promo['code']; ?>')">
                                <?php echo $promo['code']; ?>
                            </div>
                        </div>
                        
                        <div class="promo-body-small">
                            <h6 class="promo-title-small"><?php echo htmlspecialchars($promo['title']); ?></h6>
                            <p class="promo-desc-small"><?php echo htmlspecialchars($promo['description']); ?></p>
                            
                            <div class="promo-footer-small">
                                <span class="promo-date-small">
                                    <i class="far fa-calendar-alt me-1"></i>HSD: <?php echo $promo['valid_until']; ?>
                                </span>
                                <button class="btn btn-sm btn-<?php echo $promo['color']; ?> promo-btn-small" 
                                        onclick="applyPromoCode('<?php echo $promo['code']; ?>')">
                                    <i class="fas fa-check me-1"></i>Áp dụng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Xem thêm nếu cần -->
            <div class="text-center mt-4">
                <a href="view/khuyenmai.php" class="btn btn-outline-primary">
                    <i class="fas fa-gift me-2"></i>Xem tất cả khuyến mãi
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div id="footer">
            <?php include('layout/footer.php'); ?>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Thêm hiệu ứng khi click vào các chức năng
        document.addEventListener('DOMContentLoaded', function() {
            const functionItems = document.querySelectorAll('.function-item');
            
            functionItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    if (!this.getAttribute('href')) {
                        const functionName = this.querySelector('.function-name').textContent;
                        alert(`Bạn đã chọn: ${functionName}`);
                    }
                });
            });
            
            // Xử lý tìm kiếm
            const searchButton = document.querySelector('.search-box button');
            const searchInput = document.querySelector('.search-box input');
            
            if (searchButton && searchInput) {
                searchButton.addEventListener('click', function() {
                    const keyword = searchInput.value.trim();
                    if (keyword) {
                        alert(`Đang tìm kiếm: ${keyword}`);
                        // Có thể thêm code xử lý tìm kiếm ở đây
                    } else {
                        alert('Vui lòng nhập từ khóa tìm kiếm');
                        searchInput.focus();
                    }
                });
                
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        searchButton.click();
                    }
                });
            }
        });
        
        // Copy promo code to clipboard
        function copyPromoCode(code) {
            // Create a temporary textarea
            var tempTextArea = document.createElement('textarea');
            tempTextArea.value = code;
            document.body.appendChild(tempTextArea);
            tempTextArea.select();
            
            try {
                var successful = document.execCommand('copy');
                if (successful) {
                    // Show success message
                    var alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                    alert.style.zIndex = '9999';
                    alert.innerHTML = '\
                        <i class="fas fa-check-circle me-2"></i>\
                        <strong>Đã sao chép mã!</strong> Mã ' + code + ' đã được sao chép.\
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>\
                    ';
                    document.body.appendChild(alert);
                    
                    // Auto remove after 3 seconds
                    setTimeout(function() {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 3000);
                }
            } catch (err) {
                console.log('Copy failed: ' + err);
            }
            
            // Clean up
            document.body.removeChild(tempTextArea);
        }
        
        // Apply promo code
        function applyPromoCode(code) {
            // In a real application, this would redirect to booking page with promo code
            alert('Mã ' + code + ' đã được chọn! Bạn sẽ được chuyển hướng đến trang đặt phòng.');
            // window.location.href = 'datphong.php?promo=' + code;
        }
    </script>
</body>
</html>