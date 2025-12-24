<?php
// view/chiTietThanhToan.php - Enhanced Payment Details Page
session_start();
header('Content-Type: text/html; charset=utf-8');

// Check login
if (!isset($_SESSION['idKH'])) {
    header('Location: login.php');
    exit();
}

// Include model
require_once("../model/clsThanhToan.php");

// Get invoice ID from URL
$maHD = isset($_GET['maHD']) ? $_GET['maHD'] : null;

if (!$maHD) {
    echo "<script>alert('Mã hóa đơn không hợp lệ!'); window.location.href='thanhToan.php';</script>";
    exit();
}

// Get data
$model = new clsThanhToan();
$hoaDon = $model->layChiTietHoaDon($maHD);

if (!$hoaDon) {
    echo "<script>alert('Không tìm thấy hóa đơn!'); window.location.href='thanhToan.php';</script>";
    exit();
}

// Security: Check if this invoice belongs to logged-in customer
if (isset($hoaDon['idKH']) && intval($hoaDon['idKH']) !== intval($_SESSION['idKH'])) {
    echo "<script>alert('Bạn không có quyền truy cập hóa đơn này!'); window.location.href='thanhToan.php';</script>";
    exit();
}

// Generate transaction ID
$maGiaoDich = $model->taoMaGiaoDich();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Thanh Toán - <?php echo htmlspecialchars($hoaDon['maHD']); ?></title>
    
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
            background: #ffffff;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        /* Main Card */
        .payment-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        /* Header Section */
        .card-header {
            background: white;
            color: #333;
            padding: 30px;
            text-align: center;
            border-bottom: 2px solid #e9ecef;
        }
        
        .card-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .invoice-id-badge {
            font-size: 28px;
            font-weight: 700;
            background: #f8f9fa;
            color: #667eea;
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 10px;
        }
        
        /* Card Body */
        .card-body {
            padding: 30px;
        }
        
        /* Customer Info Section */
        .customer-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
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
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
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
        
        /* Services Breakdown */
        .services-section {
            margin-bottom: 25px;
        }
        
        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .service-item:last-child {
            border-bottom: none;
        }
        
        .service-info {
            flex: 1;
        }
        
        .service-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .service-meta {
            font-size: 13px;
            color: #666;
        }
        
        .service-price {
            text-align: right;
        }
        
        .unit-price {
            font-size: 14px;
            color: #666;
        }
        
        .total-price {
            font-size: 18px;
            font-weight: 700;
            color: #28a745;
        }
        
        /* Total Summary */
        .summary-section {
          background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }
        
        .summary-row.total {
            border-top: 2px solid #dee2e6;
            margin-top: 10px;
            padding-top: 15px;
        }
        
        .summary-label {
            font-size: 15px;
            color: #666;
        }
        
        .summary-value {
            font-size: 15px;
            font-weight: 600;
            color: #333;
        }
        
        .summary-row.total .summary-label {
            font-size: 18px;
            font-weight: 700;
            color: #333;
        }
        
        .summary-row.total .summary-value {
            font-size: 28px;
            font-weight: 700;
            color: #28a745;
        }
        
        /* Payment Methods */
        .payment-section {
            margin-bottom: 25px;
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .payment-method {
            border: 3px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }
        
        .payment-method:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        
        .payment-method.selected {
            border-color: #28a745;
            background: #d4edda;
        }
        
        .payment-method input[type="radio"] {
            display: none;
        }
        
        .method-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .method-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .method-desc {
            font-size: 13px;
            color: #666;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
        }
        
        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-pay {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        
        .modal-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .modal-icon.success {
            color: #28a745;
        }
        
        .modal-icon.warning {
            color: #ffc107;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .modal-message {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .modal-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-modal {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        
        .btn-confirm {
            background: #28a745;
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .payment-methods {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Loading */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <!-- Header -->
            <div class="card-header">
                <h1>
                    <i class="fas fa-file-invoice-dollar"></i>
                    Thanh Toán Hóa Đơn
                </h1>
                <div class="invoice-id-badge"><?php echo htmlspecialchars($hoaDon['maHD']); ?></div>
            </div>
            
            <!-- Body -->
            <div class="card-body">
                <!-- Invoice Info -->
                <div class="customer-section">
                    <div class="section-title">
                        <i class="fas fa-receipt"></i>
                        Thông Tin Hóa Đơn
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Mã hóa đơn</span>
                            <span class="info-value"><?php echo htmlspecialchars($hoaDon['maHD']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ngày lập</span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($hoaDon['ngayLap'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tổng tiền</span>
                            <span class="info-value" style="color: #28a745; font-size: 18px;"><?php echo number_format($hoaDon['tongTien'], 0, ',', '.'); ?>đ</span>
                        </div>
                    </div>
                </div>
                
                <!-- Services Breakdown -->
                <div class="services-section">
                    <div class="section-title">
                        <i class="fas fa-list-ul"></i>
                        Chi Tiết Dịch Vụ
                    </div>
                    <?php if(isset($hoaDon['dichVu']) && is_array($hoaDon['dichVu'])): ?>
                        <?php foreach($hoaDon['dichVu'] as $dv): ?>
                            <div class="service-item">
                                <div class="service-info">
                                    <div class="service-meta" style="margin-bottom: 8px;">
                                        <strong>Dịch vụ đã đăng ký:</strong> 
                                        <span style="color: #333; font-size: 15px;">
                                            <?php echo htmlspecialchars($dv['tenDV']); ?>
                                        </span>
                                    </div>
                                    <div class="service-meta">
                                        <strong>Số lượng:</strong> <?php echo $dv['soLuong']; ?>
                                    </div>
                                    <div class="service-meta" style="margin-top: 5px;">
                                        <i class="fas fa-door-open"></i> Phòng: 
                                        <span style="color: #999;"><?php echo isset($dv['tenPhong']) ? htmlspecialchars($dv['tenPhong']) : 'Chưa có'; ?></span>
                                        &nbsp;|&nbsp;
                                        <i class="fas fa-star"></i> Hạng: 
                                        <span style="color: #999;"><?php echo isset($dv['hangPhong']) ? htmlspecialchars($dv['hangPhong']) : 'Chưa có'; ?></span>
                                    </div>
                                </div>
                                <div class="service-price">
                                    <div class="total-price">
                                        <?php echo number_format($dv['thanhTien'], 0, ',', '.'); ?>đ
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="service-item">
                            <div class="service-info">
                                <div class="service-meta" style="margin-bottom: 8px;">
                                    <strong>Dịch vụ đã đăng ký:</strong> 
                                    <span style="color: #333; font-size: 15px;">Chi tiết dịch vụ</span>
                                </div>
                                <div class="service-meta">
                                    <strong>Số lượng:</strong> 1
                                </div>
                                <div class="service-meta" style="margin-top: 5px;">
                                    <i class="fas fa-door-open"></i> Phòng: <span style="color: #999;">Chưa có</span>
                                    &nbsp;|&nbsp;
                                    <i class="fas fa-star"></i> Hạng: <span style="color: #999;">Chưa có</span>
                                </div>
                            </div>
                            <div class="service-price">
                                <div class="total-price">
                                    <?php echo number_format($hoaDon['tongTien'], 0, ',', '.'); ?>đ
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                
                <!-- Payment Methods -->
                <div class="payment-section">
                    <div class="section-title">
                        <i class="fas fa-credit-card"></i>
                        Chọn Phương Thức Thanh Toán
                    </div>
                    <div class="payment-methods">
                        <label class="payment-method" onclick="selectMethod(this, 'cash')">
                            <input type="radio" name="phuongThuc" value="TienMat">
                            <div class="method-icon">💵</div>
                            <div class="method-name">Tiền Mặt</div>
                            <div class="method-desc">Thanh toán trực tiếp</div>
                        </label>
                        
                        <label class="payment-method" onclick="selectMethod(this, 'transfer')">
                            <input type="radio" name="phuongThuc" value="ChuyenKhoan">
                            <div class="method-icon">🏦</div>
                            <div class="method-name">Chuyển Khoản</div>
                            <div class="method-desc">Chuyển khoản ngân hàng</div>
                        </label>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn btn-back" onclick="window.location.href='thanhToan.php'">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </button>
                    <button class="btn btn-pay" id="btnPay" onclick="confirmPayment()" disabled>
                        <i class="fas fa-check-circle"></i>
                        Thanh Toán
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div class="modal" id="confirmModal">
        <div class="modal-content">
            <div class="modal-icon warning">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="modal-title">Xác nhận thanh toán?</div>
            <div class="modal-message">
                Bạn có chắc chắn muốn thanh toán hóa đơn<br>
                <strong><?php echo $hoaDon['maHD']; ?></strong> với số tiền<br>
                <strong style="color: #28a745; font-size: 20px;">
                    <?php echo number_format($hoaDon['tongTien'], 0, ',', '.'); ?>đ
                </strong>
            </div>
            <div class="modal-buttons">
                <button class="btn-modal btn-cancel" onclick="closeModal()">Hủy</button>
                <button class="btn-modal btn-confirm" onclick="processPayment()">Xác nhận</button>
            </div>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <div class="modal-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="modal-title">Thanh toán thành công!</div>
            <div class="modal-message" id="successMessage"></div>
            <button class="btn-modal btn-confirm" onclick="window.location.href='thanhToan.php'">
                Hoàn tất
            </button>
        </div>
    </div>
    
    <!-- QR Transfer Modal -->
    <div class="modal" id="qrModal">
        <div class="modal-content" style="max-width: 700px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <div class="modal-icon" style="color: #667eea;">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="modal-title">Chuyển Khoản Ngân Hàng</div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 25px;">
                <!-- Left: Bank Info -->
                <div style="padding: 25px; background: #f8f9fa; border-radius: 12px;">
                    <h4 style="margin: 0 0 20px 0; color: #333; font-size: 16px;">
                        <i class="fas fa-university"></i> Thông tin chuyển khoản
                    </h4>
                    
                    <div style="margin-bottom: 15px;">
                        <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Ngân hàng</div>
                        <div style="font-weight: 600; color: #333; font-size: 15px;">MB Bank (Military Bank)</div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Số tài khoản</div>
                        <div style="font-weight: 700; color: #333; font-size: 20px; font-family: monospace;">
                            1234567890
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Chủ tài khoản</div>
                        <div style="font-weight: 600; color: #333; font-size: 15px;">HOTEL FUN TEAM</div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Số tiền</div>
                        <div style="font-weight: 700; color: #28a745; font-size: 24px;">
                            <?php echo number_format($hoaDon['tongTien'], 0, ',', '.'); ?>đ
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <div style="font-size: 13px; color: #666; margin-bottom: 5px;">
                            <i class="fas fa-sticky-note"></i> Nội dung chuyển khoản
                        </div>
                        <div style="font-weight: 700; color: #667eea; background: white; padding: 12px; border-radius: 8px; font-size: 16px; border: 2px dashed #667eea; text-align: center;">
                            <?php echo $hoaDon['maHD']; ?>
                        </div>
                    </div>
                    
                    <div style="padding: 12px; background: #fff3cd; border-radius: 8px; font-size: 13px; color: #856404; border-left: 4px solid #ffc107;">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Lưu ý:</strong> Ghi ĐÚNG nội dung để xác nhận tự động
                    </div>
                </div>
                
                <!-- Right: QR Code -->
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 15px;">
                    <div style="margin-bottom: 15px; font-weight: 600; color: #333; text-align: center;">
                        <i class="fas fa-mobile-alt"></i><br>
                        Quét mã QR
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <img id="qrCodeImage" style="width: 200px; height: 200px; display: block;" alt="QR Code">
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #999; text-align: center;">
                        Dùng app ngân hàng để quét
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 25px; display: flex; gap: 12px; justify-content: center;">
                <button class="btn-modal btn-cancel" onclick="closeQRModal()" style="min-width: 150px;">
                    <i class="fas fa-times"></i> Hủy
                </button>
                <button class="btn-modal btn-confirm" onclick="confirmTransfer()" style="min-width: 150px;">
                    <i class="fas fa-check"></i> Đã chuyển khoản
                </button>
            </div>
        </div>
    </div>
    
    <script>
        let selectedMethod = null;
        
        function selectMethod(element, method) {
            // Remove selected class from all
            document.querySelectorAll('.payment-method').forEach(function(el) {
                el.classList.remove('selected');
            });
            
            // Add selected class to clicked
            element.classList.add('selected');
            element.querySelector('input[type="radio"]').checked = true;
            
            selectedMethod = method;
            
            // Enable pay button
            document.getElementById('btnPay').disabled = false;
        }
        
        function confirmPayment() {
            if (!selectedMethod) {
                alert('Vui lòng chọn phương thức thanh toán!');
                return;
            }
            document.getElementById('confirmModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('confirmModal').classList.remove('active');
        }
        
        function processPayment() {
            var phuongThuc = document.querySelector('input[name="phuongThuc"]:checked').value;
            
            // If bank transfer, show QR modal
            if (phuongThuc === 'ChuyenKhoan') {
                closeModal();
                showQRModal();
                return;
            }
            
            // Cash payment - process immediately
            var btnConfirm = document.querySelector('#confirmModal .btn-confirm');
            btnConfirm.innerHTML = '<span class="loading"></span> Đang xử lý...';
            btnConfirm.disabled = true;
            
            var formData = new FormData();
            formData.append('maHD', '<?php echo $hoaDon["maHD"]; ?>');
            formData.append('maGD', '<?php echo $maGiaoDich; ?>');
            formData.append('phuongThuc', phuongThuc);
            formData.append('tongTien', <?php echo $hoaDon['tongTien']; ?>);
            
            fetch('../controller/cThanhToan.php?action=xacnhan', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                closeModal();
                
                if (data.success) {
                    document.getElementById('successMessage').innerHTML = 
                        'Hóa đơn đã được thanh toán thành công!<br>' +
                        '<strong>Mã giao dịch: ' + data.maGD + '</strong>';
                    document.getElementById('successModal').classList.add('active');
                } else {
                    alert('Lỗi: ' + data.message);
                    btnConfirm.innerHTML = 'Xác nhận';
                    btnConfirm.disabled = false;
                }
            })
            .catch(function(error) {
                closeModal();
                alert('Có lỗi xảy ra: ' + error.message);
                btnConfirm.innerHTML = 'Xác nhận';
                btnConfirm.disabled = false;
            });
        }
        
        function showQRModal() {
            // Generate dynamic QR code using VietQR API
            var amount = <?php echo $hoaDon['tongTien']; ?>;
            var content = '<?php echo $hoaDon["maHD"]; ?>';
            var accountNo = '0001537125921';
            var accountName = 'LE HONG QUAN';
            
            // VietQR API format: bank-account-template.png?amount=xxx&addInfo=xxx&accountName=xxx
            var qrUrl = 'https://img.vietqr.io/image/970422-' + accountNo + '-compact2.png?amount=' + amount + '&addInfo=' + encodeURIComponent(content) + '&accountName=' + encodeURIComponent(accountName);
            
            document.getElementById('qrCodeImage').src = qrUrl;
            document.getElementById('qrModal').classList.add('active');
        }
        
        function closeQRModal() {
            document.getElementById('qrModal').classList.remove('active');
        }
        
        function confirmTransfer() {
            var btnConfirm = document.querySelector('#qrModal .btn-confirm');
            btnConfirm.innerHTML = '<span class="loading"></span> Đang xử lý...';
            btnConfirm.disabled = true;
            
            var formData = new FormData();
            formData.append('maHD', '<?php echo $hoaDon["maHD"]; ?>');
            formData.append('maGD', '<?php echo $maGiaoDich; ?>');
            formData.append('phuongThuc', 'ChuyenKhoan');
            formData.append('tongTien', <?php echo $hoaDon['tongTien']; ?>);
            
            fetch('../controller/cThanhToan.php?action=xacnhan', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                closeQRModal();
                
                if (data.success) {
                    document.getElementById('successMessage').innerHTML = 
                        'Đã ghi nhận chuyển khoản của bạn!<br>' +
                        '<strong>Mã giao dịch: ' + data.maGD + '</strong><br>' +
                        '<small>Vui lòng chờ xác nhận thanh toán</small>';
                    document.getElementById('successModal').classList.add('active');
                } else {
                    alert('Lỗi: ' + data.message);
                    btnConfirm.innerHTML = '<i class="fas fa-check"></i> Đã chuyển khoản';
                    btnConfirm.disabled = false;
                }
            })
            .catch(function(error) {
                closeQRModal();
                alert('Có lỗi xảy ra: ' + error.message);
                btnConfirm.innerHTML = '<i class="fas fa-check"></i> Đã chuyển khoản';
                btnConfirm.disabled = false;
            });
        }
    </script>
</body>
</html>