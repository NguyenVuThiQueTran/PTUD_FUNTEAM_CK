<?php
// view/khuyenmai.php
session_start();

// Xác định đường dẫn
$current_file = $_SERVER['PHP_SELF'];
$is_in_view_folder = (strpos($current_file, '/view/') !== false);

if ($is_in_view_folder) {
    $header_path = '../layout/header_lh.php';
    $index_path = '../index.php';
} else {
    $header_path = 'layout/header.php';
    $index_path = 'index.php';
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

// Kiểm tra PHP version
$php_version = phpversion();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khuyến Mãi - Khách sạn FunTeam</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .promo-hero {
            background: linear-gradient(rgba(13, 34, 172, 0.9), rgba(30, 60, 114, 0.9)), 
                        url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
            text-align: center;
            border-radius: 0 0 30px 30px;
        }
        
        .promo-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
        }
        
        .promo-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .promo-header {
            background: linear-gradient(135deg, var(--promo-color), #1e3c72);
            color: white;
            padding: 25px 20px;
            text-align: center;
            position: relative;
        }
        
        .promo-discount {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .promo-code {
            background: rgba(255,255,255,0.2);
            border: 2px dashed white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 2px;
            display: inline-block;
            margin: 15px 0;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .promo-code:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.05);
        }
        
        .promo-body {
            padding: 25px;
        }
        
        .promo-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .promo-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: gold;
            color: #333;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .filter-buttons {
            margin-bottom: 30px;
        }
        
        .filter-btn {
            margin: 5px;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .filter-btn.active {
            background: #0d22ac;
            color: white;
        }
        
        .timer {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
            border-left: 4px solid #0d22ac;
        }
        
        .timer-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0d22ac;
        }
        
        .timer-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
        }
        
        .how-to-use {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        
        .step {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background: #0d22ac;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .newsletter-section {
            background: linear-gradient(135deg, #0d22ac, #1e3c72);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-top: 50px;
        }
        
        @media (max-width: 768px) {
            .promo-hero {
                padding: 50px 0;
            }
            
            .promo-discount {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- PHP Version Warning (chỉ hiện trong development) -->
    
    
    <!-- Header -->
    <?php include($header_path); ?>
    
    <!-- Hero Section -->
    <div class="promo-hero">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-gift me-3"></i>Khuyến Mãi Hấp Dẫn
            </h1>
            <p class="lead mb-4">Cơ hội trải nghiệm dịch vụ đẳng cấp với giá ưu đãi đặc biệt</p>
            
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container">
        <!-- Filter Buttons -->
        <div class="filter-buttons text-center">
            <button class="btn btn-outline-primary filter-btn active" data-filter="all">Tất cả</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="high">Giảm giá cao</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="family">Gia đình</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="dining">Ăn uống</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="member">Thành viên</button>
        </div>
        
        <!-- Promotions Grid -->
        <div class="row g-4" id="promotionsGrid">
            <?php foreach ($promotions as $promo): ?>
            <div class="col-lg-4 col-md-6" data-category="<?php echo strtolower($promo['title']); ?>">
                <div class="promo-card" style="--promo-color: var(--bs-<?php echo $promo['color']; ?>)">
                    <?php if (strpos($promo['discount'], '25') !== false || strpos($promo['discount'], '30') !== false || strpos($promo['discount'], '40') !== false): ?>
                    <div class="promo-badge">
                        <i class="fas fa-fire me-1"></i> HOT
                    </div>
                    <?php endif; ?>
                    
                    <div class="promo-header">
                        <div class="promo-icon">
                            <i class="fas <?php echo $promo['icon']; ?>"></i>
                        </div>
                        <div class="promo-discount"><?php echo $promo['discount']; ?></div>
                        <h4 class="mb-3"><?php echo htmlspecialchars($promo['title']); ?></h4>
                        <div class="promo-code" onclick="copyPromoCode('<?php echo $promo['code']; ?>')">
                            <i class="fas fa-ticket-alt me-2"></i><?php echo $promo['code']; ?>
                        </div>
                    </div>
                    
                    <div class="promo-body">
                        <p class="mb-4"><?php echo htmlspecialchars($promo['description']); ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <small class="text-muted">Hạn sử dụng:</small>
                                <div class="fw-bold text-<?php echo $promo['color']; ?>">
                                    <?php echo $promo['valid_until']; ?>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-<?php echo $promo['color']; ?>" 
                                    onclick="applyPromoCode('<?php echo $promo['code']; ?>')">
                                <i class="fas fa-check me-1"></i> Áp dụng
                            </button>
                        </div>
                        
                        <!-- Timer -->
                        <div class="timer">
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="timer-number" id="days-<?php echo $promo['id']; ?>">00</div>
                                    <div class="timer-label">Ngày</div>
                                </div>
                                <div class="col-3">
                                    <div class="timer-number" id="hours-<?php echo $promo['id']; ?>">00</div>
                                    <div class="timer-label">Giờ</div>
                                </div>
                                <div class="col-3">
                                    <div class="timer-number" id="minutes-<?php echo $promo['id']; ?>">00</div>
                                    <div class="timer-label">Phút</div>
                                </div>
                                <div class="col-3">
                                    <div class="timer-number" id="seconds-<?php echo $promo['id']; ?>">00</div>
                                    <div class="timer-label">Giây</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- How to Use Section -->
        <div class="how-to-use">
            <h3 class="section-title mb-4">
                <i class="fas fa-question-circle me-2"></i>Cách Sử Dụng Mã Khuyến Mãi
            </h3>
            
            <div class="step">
                <div class="step-number">1</div>
                <div>
                    <h5 class="fw-bold">Chọn khuyến mãi</h5>
                    <p class="mb-0">Chọn chương trình khuyến mãi phù hợp và nhấn nút "Áp dụng" hoặc sao chép mã</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">2</div>
                <div>
                    <h5 class="fw-bold">Đặt phòng trực tuyến</h5>
                    <p class="mb-0">Truy cập trang đặt phòng và chọn loại phòng, ngày nhận/trả phòng</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">3</div>
                <div>
                    <h5 class="fw-bold">Nhập mã khuyến mãi</h5>
                    <p class="mb-0">Tại bước thanh toán, nhập mã khuyến mãi vào ô "Mã giảm giá"</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">4</div>
                <div>
                    <h5 class="fw-bold">Xác nhận đặt phòng</h5>
                    <p class="mb-0">Hoàn tất thông tin và xác nhận đặt phòng để nhận ưu đãi</p>
                </div>
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Lưu ý:</strong> Mỗi mã khuyến mãi chỉ sử dụng được một lần và không áp dụng đồng thời với các chương trình khuyến mãi khác.
            </div>
        </div>
        
        <!-- Newsletter Section -->
        <div class="newsletter-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-3">
                        <i class="fas fa-envelope me-2"></i>Đăng ký nhận khuyến mãi
                    </h3>
                    <p class="mb-0">Nhận thông báo về các chương trình khuyến mãi mới nhất qua email</p>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Email của bạn" id="newsletterEmail">
                        <button class="btn btn-light" type="button" onclick="subscribeNewsletter()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back to Home -->
        
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Initialize countdown timers
        function initializeTimers() {
            <?php foreach ($promotions as $promo): ?>
            // Set expiration date (example: 30 days from now)
            var expireDate<?php echo $promo['id']; ?> = new Date();
            expireDate<?php echo $promo['id']; ?>.setDate(expireDate<?php echo $promo['id']; ?>.getDate() + 30);
            
            // Update timer every second
            setInterval(function() {
                updateTimer('<?php echo $promo['id']; ?>', expireDate<?php echo $promo['id']; ?>);
            }, 1000);
            
            // Initial update
            updateTimer('<?php echo $promo['id']; ?>', expireDate<?php echo $promo['id']; ?>);
            <?php endforeach; ?>
        }
        
        function updateTimer(promoId, expireDate) {
            var now = new Date().getTime();
            var distance = expireDate - now;
            
            if (distance < 0) {
                document.getElementById('days-' + promoId).innerHTML = "00";
                document.getElementById('hours-' + promoId).innerHTML = "00";
                document.getElementById('minutes-' + promoId).innerHTML = "00";
                document.getElementById('seconds-' + promoId).innerHTML = "00";
                return;
            }
            
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('days-' + promoId).innerHTML = (days < 10 ? '0' : '') + days;
            document.getElementById('hours-' + promoId).innerHTML = (hours < 10 ? '0' : '') + hours;
            document.getElementById('minutes-' + promoId).innerHTML = (minutes < 10 ? '0' : '') + minutes;
            document.getElementById('seconds-' + promoId).innerHTML = (seconds < 10 ? '0' : '') + seconds;
        }
        
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
                        <strong>Đã sao chép mã!</strong> Mã ' + code + ' đã được sao chép vào clipboard.\
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
            alert('Mã ' + code + ' đã được áp dụng! Bạn sẽ được chuyển hướng đến trang đặt phòng.');
            // window.location.href = 'datphong.php?promo=' + code;
        }
        
        // Filter promotions
        var filterButtons = document.querySelectorAll('.filter-btn');
        for (var i = 0; i < filterButtons.length; i++) {
            filterButtons[i].addEventListener('click', function() {
                // Update active button
                var allButtons = document.querySelectorAll('.filter-btn');
                for (var j = 0; j < allButtons.length; j++) {
                    allButtons[j].classList.remove('active');
                }
                this.classList.add('active');
                
                var filter = this.getAttribute('data-filter');
                var cards = document.querySelectorAll('#promotionsGrid > div');
                
                for (var k = 0; k < cards.length; k++) {
                    var card = cards[k];
                    if (filter === 'all') {
                        card.style.display = 'block';
                    } else if (filter === 'high') {
                        var discountElement = card.querySelector('.promo-discount');
                        var discountText = discountElement ? discountElement.textContent : '';
                        var discountValue = parseInt(discountText);
                        card.style.display = discountValue >= 25 ? 'block' : 'none';
                    } else if (filter === 'family') {
                        var titleElement = card.querySelector('h4');
                        var title = titleElement ? titleElement.textContent.toLowerCase() : '';
                        card.style.display = title.indexOf('gia đình') !== -1 || title.indexOf('family') !== -1 ? 'block' : 'none';
                    } else if (filter === 'dining') {
                        var titleElement = card.querySelector('h4');
                        var title = titleElement ? titleElement.textContent.toLowerCase() : '';
                        card.style.display = title.indexOf('ăn') !== -1 || title.indexOf('dining') !== -1 ? 'block' : 'none';
                    } else if (filter === 'member') {
                        var titleElement = card.querySelector('h4');
                        var title = titleElement ? titleElement.textContent.toLowerCase() : '';
                        card.style.display = title.indexOf('thành viên') !== -1 || title.indexOf('member') !== -1 ? 'block' : 'none';
                    }
                }
            });
        }
        
        // Newsletter subscription
        function subscribeNewsletter() {
            var email = document.getElementById('newsletterEmail').value;
            
            if (!email || email.indexOf('@') === -1) {
                alert('Vui lòng nhập địa chỉ email hợp lệ!');
                return;
            }
            
            // In a real application, this would send to server
            alert('Cảm ơn bạn đã đăng ký nhận khuyến mãi! Chúng tôi sẽ gửi thông tin đến ' + email);
            document.getElementById('newsletterEmail').value = '';
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeTimers();
            
            // Add animation to cards on scroll
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });
            
            var cards = document.querySelectorAll('.promo-card');
            for (var i = 0; i < cards.length; i++) {
                var card = cards[i];
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            }
        });
    </script>
</body>
</html>