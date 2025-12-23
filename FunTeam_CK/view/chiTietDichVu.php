<?php
// view/chiTietDichVu.php - Standalone file
session_start();
header('Content-Type: text/html; charset=utf-8');

// Ngăn cache để luôn lấy data mới
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check login
if (!isset($_SESSION['idKH'])) {
    header('Location: login.php');
    exit();
}

$idKH = $_SESSION['idKH'];

// Include model
require_once("../model/clsDichVu.php");

// Get service ID from URL
$maDV = isset($_GET['maDV']) ? $_GET['maDV'] : null;

if (!$maDV) {
    die("Mã dịch vụ không hợp lệ!");
}

// Get data
$model = new clsDichVu();
$chiTiet = $model->layChiTietDichVu($maDV);

if (!$chiTiet) {
    die("Không tìm thấy dịch vụ!");
}

// Get available quantity from query result
$soLuongKhaDung = isset($chiTiet['soLuongKhaDung']) ? intval($chiTiet['soLuongKhaDung']) : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Dịch Vụ - <?php echo htmlspecialchars($chiTiet['tenDV']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }
        
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 12px 24px;
            background: white;
            border: 2px solid #007bff;
            color: #007bff;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            z-index: 1000;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .back-button:hover {
            background: #007bff;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .container {
            max-width: 1400px;
            margin: 100px auto 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .content-wrapper {
            display: flex;
            min-height: 600px;
        }
        
        /* IMAGE SLIDER */
        .image-section {
            width: 40%;
            background: #f8f9fa;
            padding: 30px;
            position: relative;
        }
        
        .slider-container {
            position: relative;
            width: 100%;
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        
        .slide {
            display: none;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 40px;
        }
        
        .slide.active {
            display: flex;
        }
        
        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.9);
            color: #333;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 24px;
            font-weight: bold;
            z-index: 10;
            transition: all 0.3s;
        }
        
        .slider-btn:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
        }
        
        .slider-btn.prev {
            left: 20px;
        }
        
        .slider-btn.next {
            right: 20px;
        }
        
        .slider-dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }
        
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .dot.active {
            background: white;
            width: 30px;
            border-radius: 6px;
        }
        
        /* INFO SECTION */
        .info-section {
            width: 60%;
            padding: 40px;
        }
        
        .service-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 25px;
            margin-bottom: 25px;
        }
        
        .service-title {
            font-size: 36px;
            color: #1a1a1a;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .service-meta {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .rating {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #ffc107;
            font-size: 20px;
            font-weight: 600;
        }
        
        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .service-price {
            font-size: 42px;
            color: #007bff;
            font-weight: 800;
            margin-bottom: 25px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .info-item {
            padding: 18px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #007bff;
        }
        
        .info-item label {
            display: block;
            font-weight: 600;
            color: #666;
            font-size: 13px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        
        .info-item .value {
            color: #1a1a1a;
            font-size: 18px;
            font-weight: 600;
        }
        
        .quantity-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
        }
        
        .quantity-label {
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .quantity-btn {
            width: 50px;
            height: 50px;
            border: 2px solid #007bff;
            background: white;
            color: #007bff;
            border-radius: 10px;
            cursor: pointer;
            font-size: 24px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .quantity-btn:hover:not(:disabled) {
            background: #007bff;
            color: white;
            transform: scale(1.1);
        }
        
        .quantity-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        
        .quantity-input {
            width: 100px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #e9ecef;
            border-radius: 10px;
        }
        
        .btn-book {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-book:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(40,167,69,0.4);
        }
        
        .btn-book:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        .description {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #e9ecef;
        }
        
        .description h3 {
            color: #1a1a1a;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .description p {
            color: #666;
            line-height: 1.8;
            font-size: 15px;
        }
        
        /* MODAL XÁC NHẬN */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.show {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 15px;
            max-width: 900px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 50px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-header h2 {
            font-size: 24px;
            margin: 0;
        }
        
        .modal-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
        }
        
        .modal-section {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
        .modal-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        
        .modal-info {
            margin-bottom: 15px;
        }
        
        .modal-info label {
            display: block;
            font-weight: 600;
            color: #666;
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .modal-info .value {
            color: #1a1a1a;
            font-size: 16px;
            font-weight: 600;
        }
        
        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }
        
        .btn-modal {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        .btn-confirm {
            background: #28a745;
            color: white;
        }
        
        .btn-confirm:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 8px;
            display: none;
            font-weight: 600;
        }
        
        .alert.show {
            display: block;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <a href="dangKyDichVuBoSung.php" class="back-button">← Quay lại danh sách</a>
    
    <div class="container">
        <div class="content-wrapper">
            <!-- IMAGE SLIDER -->
            <div class="image-section">
                <div class="slider-container">
                    <?php 
                    // Tạo 3 slide mẫu
                    $slides = array(
                        $chiTiet['tenDV'],
                        $chiTiet['tenDV'] . ' - Premium',
                        $chiTiet['tenDV'] . ' - Exclusive'
                    );
                    
                    foreach ($slides as $index => $slide) {
                        $activeClass = ($index == 0) ? 'active' : '';
                        echo '<div class="slide ' . $activeClass . '">' . htmlspecialchars($slide) . '</div>';
                    }
                    ?>
                    
                    <button class="slider-btn prev" onclick="changeSlide(-1)">‹</button>
                    <button class="slider-btn next" onclick="changeSlide(1)">›</button>
                    
                    <div class="slider-dots">
                        <?php for ($i = 0; $i < count($slides); $i++): ?>
                            <span class="dot <?php echo $i == 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $i; ?>)"></span>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            
            <!-- INFO SECTION -->
            <div class="info-section">
                <div class="service-header">
                    <h1 class="service-title"><?php echo htmlspecialchars($chiTiet['tenDV']); ?></h1>
                </div>
                
                <div class="service-price">
                    <?php echo number_format($chiTiet['donGia'], 0, ',', '.'); ?>đ
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <label>Loại dịch vụ</label>
                        <div class="value"><?php echo ucfirst(htmlspecialchars($chiTiet['loaiDV'])); ?></div>
                    </div>
                    <div class="info-item">
                        <label>Khung giờ</label>
                        <div class="value">Linh hoạt</div>
                    </div>
                    <div class="info-item">
                        <label>Số lượng khả dụng</label>
                        <div class="value"><?php echo $soLuongKhaDung; ?> slot</div>
                    </div>
                    <div class="info-item">
                        <label>Trạng thái</label>
                        <div class="value"><?php echo $soLuongKhaDung > 0 ? 'Còn' : 'Hết hàng'; ?></div>
                    </div>
                </div>
                
                <div id="alertBox" class="alert"></div>
                
                <div class="quantity-section">
                    <div class="quantity-label">Chọn số lượng:</div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseQuantity()" <?php echo $soLuongKhaDung <= 0 ? 'disabled' : ''; ?>>-</button>
                        <input type="number" id="quantity" class="quantity-input" 
                               value="<?php echo $soLuongKhaDung > 0 ? '1' : '0'; ?>" 
                               min="<?php echo $soLuongKhaDung > 0 ? '1' : '0'; ?>" 
                               max="<?php echo $soLuongKhaDung; ?>" 
                               readonly>
                        <button class="quantity-btn" onclick="increaseQuantity()" <?php echo $soLuongKhaDung <= 0 ? 'disabled' : ''; ?>>+</button>
                    </div>
                </div>
                
                <button class="btn-book" onclick="openConfirmModal()" <?php echo $soLuongKhaDung <= 0 ? 'disabled' : ''; ?>>
                    <?php echo $soLuongKhaDung > 0 ? 'Chọn Dịch Vụ' : 'Dịch vụ không khả dụng'; ?>
                </button>
                
                <?php if (!empty($chiTiet['moTa'])): ?>
                <div class="description">
                    <h3>Mô tả dịch vụ</h3>
                    <p><?php echo nl2br(htmlspecialchars($chiTiet['moTa'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- MODAL XÁC NHẬN -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Xác Nhận Thông Tin Đặt Dịch Vụ</h2>
            </div>
            <div class="modal-body">
                <!-- Thông tin khách hàng -->
                <div class="modal-section">
                    <h3>Thông Tin Khách Hàng</h3>
                    <div class="modal-info">
                        <label>Họ và tên:</label>
                        <div class="value" id="khHoTen">-</div>
                    </div>
                    <div class="modal-info">
                        <label>Email:</label>
                        <div class="value" id="khEmail">-</div>
                    </div>
                    <div class="modal-info">
                        <label>Số điện thoại:</label>
                        <div class="value" id="khSoDienThoai">-</div>
                    </div>
                </div>
                
                <!-- Thông tin dịch vụ -->
                <div class="modal-section">
                    <h3>Thông Tin Dịch Vụ</h3>
                    <div class="modal-info">
                        <label>Tên dịch vụ:</label>
                        <div class="value"><?php echo htmlspecialchars($chiTiet['tenDV']); ?></div>
                    </div>
                    <div class="modal-info">
                        <label>Loại dịch vụ:</label>
                        <div class="value"><?php echo ucfirst(htmlspecialchars($chiTiet['loaiDV'])); ?></div>
                    </div>
                    <div class="modal-info">
                        <label>Đơn giá:</label>
                        <div class="value"><?php echo number_format($chiTiet['donGia'], 0, ',', '.'); ?>đ</div>
                    </div>
                    <div class="modal-info">
                        <label>Khung giờ:</label>
                        <div class="value">Linh hoạt</div>
                    </div>
                    <div class="modal-info">
                        <label>Số lượng:</label>
                        <div class="value" id="modalSoLuong">1</div>
                    </div>
                    <div class="modal-info">
                        <label>Tổng tiền:</label>
                        <div class="value" style="color: #007bff; font-size: 20px;" id="modalTongTien">0đ</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-cancel" onclick="closeConfirmModal()">Hủy</button>
                <button class="btn-modal btn-confirm" onclick="confirmBooking()">Xác Nhận Đặt Dịch Vụ</button>
            </div>
        </div>
    </div>
    
    <script>
        const maxQuantity = <?php echo $soLuongKhaDung; ?>;
        const donGia = <?php echo $chiTiet['donGia']; ?>;
        const maDV = '<?php echo $chiTiet['maDV']; ?>';
        let currentSlide = 0;
        const totalSlides = <?php echo count($slides); ?>;
        
        // SLIDER FUNCTIONS
        function showSlide(n) {
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            
            if (n >= totalSlides) currentSlide = 0;
            if (n < 0) currentSlide = totalSlides - 1;
            
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }
        
        function changeSlide(n) {
            currentSlide += n;
            showSlide(currentSlide);
        }
        
        function goToSlide(n) {
            currentSlide = n;
            showSlide(currentSlide);
        }
        
        // Auto slide every 3 seconds
        setInterval(() => {
            changeSlide(1);
        }, 3000);
        
        // QUANTITY FUNCTIONS
        function increaseQuantity() {
            const input = document.getElementById('quantity');
            const current = parseInt(input.value);
            if (current < maxQuantity) {
                input.value = current + 1;
            }
        }
        
        function decreaseQuantity() {
            const input = document.getElementById('quantity');
            const current = parseInt(input.value);
            if (current > 1) {
                input.value = current - 1;
            }
        }
        
        // ALERT FUNCTIONS
        function showAlert(message, type) {
            const alertBox = document.getElementById('alertBox');
            alertBox.textContent = message;
            alertBox.className = 'alert ' + type + ' show';
            
            setTimeout(() => {
                alertBox.classList.remove('show');
            }, 5000);
        }
        
        // MODAL FUNCTIONS
        function openConfirmModal() {
            const quantity = parseInt(document.getElementById('quantity').value);
            
            if (maxQuantity <= 0) {
                showAlert('Dịch vụ hiện không khả dụng!', 'error');
                return;
            }
            
            if (quantity <= 0) {
                showAlert('Vui lòng chọn số lượng hợp lệ!', 'error');
                return;
            }
            
            if (quantity > maxQuantity) {
                showAlert(`Số lượng không đủ! Chỉ còn ${maxQuantity} slot`, 'error');
                return;
            }
            
            // Lấy idKH từ session PHP
            const idKH = <?php echo intval($idKH); ?>;
            
            // Lấy thông tin khách hàng
            fetch(`../controller/cDichVu.php?action=laythongtin&idKH=${idKH}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    
                    if (data && !data.error) {
                        document.getElementById('khHoTen').textContent = data.hoTen || '-';
                        document.getElementById('khEmail').textContent = data.email || '-';
                        document.getElementById('khSoDienThoai').textContent = data.soDienThoai || 'Chưa cập nhật';
                        
                        document.getElementById('modalSoLuong').textContent = quantity;
                        const tongTien = quantity * donGia;
                        document.getElementById('modalTongTien').textContent = new Intl.NumberFormat('vi-VN').format(tongTien) + 'đ';
                        
                        document.getElementById('confirmModal').classList.add('show');
                    } else {
                        showAlert('Không tìm thấy thông tin khách hàng!', 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showAlert('Có lỗi xảy ra khi lấy thông tin: ' + error.message, 'error');
                });
        }
        
        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.remove('show');
        }
        
        function confirmBooking() {
            const quantity = parseInt(document.getElementById('quantity').value);
            const idKH = <?php echo intval($idKH); ?>; // Lấy từ session PHP
            
            const formData = new FormData();
            formData.append('idKH', idKH);
            formData.append('maDV', maDV);
            formData.append('soLuong', quantity);
            
            // Disable button để tránh click nhiều lần
            const btnConfirm = document.querySelector('.btn-confirm');
            btnConfirm.disabled = true;
            btnConfirm.textContent = 'Đang xử lý...';
            
            fetch('../controller/cDichVu.php?action=xacnhan', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Response:', data);
                
                if (data.success) {
                    closeConfirmModal();
                    showAlert('✅ ' + data.message + ` (Mã hóa đơn: ${data.maHD})`, 'success');
                    
                    setTimeout(() => {
                        window.location.href = 'dangKyDichVuBoSung.php';
                    }, 2000);
                } else {
                    btnConfirm.disabled = false;
                    btnConfirm.textContent = 'Xác Nhận Đặt Dịch Vụ';
                    showAlert('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                btnConfirm.disabled = false;
                btnConfirm.textContent = 'Xác Nhận Đặt Dịch Vụ';
                showAlert('Có lỗi xảy ra: ' + error.message, 'error');
            });
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('confirmModal');
            if (event.target == modal) {
                closeConfirmModal();
            }
        }
    </script>
</body>
</html>