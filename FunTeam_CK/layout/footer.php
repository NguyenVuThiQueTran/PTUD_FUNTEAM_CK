<?php
// layout/footer.php
?>
<style>
/* Footer container */
#footer {
    
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #333;
    width: 100%;
    padding: 2.5rem 0 0 0; /* Chỉ padding trên */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    border-top: none; /* XÓA border-top ở đây */
    
}

/* Container chính */
.footer-container {
    border-top: solid 2px #0d6efd;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
   
}

/* Hàng chứa 3 cột */
.footer-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 30px;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    padding-top: 25px;
}

/* Mỗi cột chiếm 1/3 */
.footer-col {
    flex: 1;
    min-width: 300px;
    padding: 0 15px;
}

/* Tiêu đề cột */
.footer-col h3 {
    color: #0d6efd;
    font-weight: 600;
    margin-bottom: 1.2rem;
    font-size: 1.2rem;
    position: relative;
    padding-bottom: 0.5rem;
}

.footer-col h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 2px;
    background-color: #0d6efd;
}

/* Nội dung trong cột */
.footer-col p {
    color: #495057;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

/* Link trong cột */
.footer-col a {
    color: #495057;
    text-decoration: none;
    font-size: 0.95rem;
    display: block;
    margin-bottom: 0.8rem;
    transition: all 0.3s ease;
}

.footer-col a:hover {
    color: #0d6efd;
    transform: translateX(5px);
}

/* Footer bottom wrapper - FULL WIDTH */
.footer-bottom-wrapper {
    background-color: #343a40;
    width: 100%;
    
}

/* Footer bottom content */
.footer-bottom {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.2rem 15px;
    text-align: center;
    color: white;
}

.footer-bottom p {
    margin: 0;
    font-size: 0.95rem;
    color: #e9ecef;
}

/* Responsive cho mobile */
@media (max-width: 768px) {
    .footer-row {
        flex-direction: column;
        gap: 20px;
    }
    
    .footer-col {
        min-width: 100%;
        text-align: center;
        padding: 0;
    }
    
    .footer-col h3::after {
        left: 50%;
        transform: translateX(-50%);
    }
}
</style>

<div id="footer">
    <div class="footer-container">
        <div class="footer-row">
            <!-- Cột 1: Thông tin -->
            <div class="footer-col">
                <h3>Thông tin</h3>
                <p>Chào mừng các bạn đến với Hệ thống quản lý khách sạn - giải pháp toàn diện cho việc quản lý và vận hành khách sạn một cách chuyên nghiệp và hiệu quả.</p>
            </div>
            
            <!-- Cột 2: Liên kết -->
            <div class="footer-col">
                <h3>Liên kết</h3>
                <a href="http://funteam@gmail.com/" target="_blank">Website khách sạn</a>
                <a href="#" target="_blank">Đặt phòng trực tuyến</a>
                <a href="#" target="_blank">Dịch vụ khách sạn</a>
                <a href="#" target="_blank">Khuyến mãi</a>
                <a href="#" target="_blank">Tuyển dụng</a>
            </div>
            
            <!-- Cột 3: Liên hệ -->
            <div class="footer-col">
                <h3>Liên hệ</h3>
                <p><strong>Hệ thống quản lý khách sạn FunTeam</strong></p>
                <p><i class="fas fa-map-marker-alt" style="color: #0d6efd; margin-right: 8px;"></i> 12 Nguyễn Văn Bảo, phường 4, Gò Vấp, TP.HCM</p>
                <p><i class="fas fa-phone" style="color: #0d6efd; margin-right: 8px;"></i> Phone: 02 234 5667</p>
                <p><i class="fas fa-envelope" style="color: #0d6efd; margin-right: 8px;"></i> E-mail: funteam@gmail.com</p>
                <p><i class="fas fa-clock" style="color: #0d6efd; margin-right: 8px;"></i> Hỗ trợ: 24/7</p>
            </div>
        </div>
    </div>
    
    <!-- Footer bottom - NẰM NGOÀI container để full width -->
    <div class="footer-bottom-wrapper">
        <div class="footer-bottom">
            <p>Copyright © <?php echo date('Y'); ?> - Phát triển bởi Trung tâm Quản trị Hệ thống</p>
        </div>
    </div>
</div>