<?php
// PTUD_FunTeam-main/view/lienhe.php
session_start();

// Vì file đang ở trong thư mục view/
$header_path = '../layout/header_lh.php';
$index_path = '../index.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - Hệ thống quản lý khách sạn</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
            width: 100%;
        }
        
        .contact-header {
            background: linear-gradient(135deg, #76ed67ff, #91cd89ff);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            text-align: center;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .contact-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        
        .contact-card:hover {
            transform: translateY(-3px);
        }
        
        .contact-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #0d22ac;
        }
        
        .contact-form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #0d22ac, #1e3c72);
            color: white;
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 5px;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, #1e3c72, #0d22ac);
            color: white;
        }
        
        .map-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-top: 20px;
        }
        
        .contact-info-item {
            padding: 12px;
            border-left: 3px solid #0d22ac;
            background-color: #f8f9ff;
            margin-bottom: 12px;
            border-radius: 0 8px 8px 0;
        }
        
        .quick-contact-item {
            padding: 15px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            transition: all 0.3s;
            height: 100%;
        }
        
        .quick-contact-item:hover {
            background-color: #f8f9ff;
            border-color: #0d22ac;
        }
        
        .faq-accordion .accordion-button {
            background-color: #f8f9ff;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include($header_path); ?>
    
    <div class="container mt-3">
        <!-- Contact Header -->
        <div class="contact-header">
            <h1 class="h2 fw-bold"><i class="fas fa-headset me-2"></i>Liên Hệ</h1>
            <p class="mb-0">Hỗ trợ khách hàng </p>
        </div>
        
        <div class="row g-4">
            <!-- Contact Information -->
            <div class="col-lg-6">
                <div class="contact-card">
                    <div class="card-body">
                        <h4 class="card-title text-primary mb-4">
                            <i class="fas fa-info-circle me-2"></i>Thông Tin Liên Hệ
                        </h4>
                        
                        <div class="contact-info-item">
                            <h6 class="fw-bold">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i> Địa chỉ
                            </h6>
                            <p class="mb-0">12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, TP. Hồ Chí Minh</p>
                        </div>
                        
                        <div class="contact-info-item">
                            <h6 class="fw-bold">
                                <i class="fas fa-phone text-success me-2"></i> Điện thoại
                            </h6>
                            <p class="mb-0">Hotline: <strong class="text-primary">0909090909</strong></p>
                            <p class="mb-0">Điện thoại: <strong>(028) 1234 5678</strong></p>
                        </div>
                        
                        <div class="contact-info-item">
                            <h6 class="fw-bold">
                                <i class="fas fa-envelope text-warning me-2"></i> Email
                            </h6>
                            <p class="mb-0">Hỗ trợ: <strong>hotro@funteamhotel.com</strong></p>
                            
                        </div>
                        
                        <div class="contact-info-item">
                            <h6 class="fw-bold">
                                <i class="fas fa-clock text-info me-2"></i> Giờ hoạt động của Trung tâm chăm sóc khách hàng
                            </h6>
                            <p class="mb-0">Thứ 2 - Thứ 7: 7:00 - 22:00</p>
                            <p class="mb-0">Chủ nhật: 7:00 - 18:00</p>
                        </div>
                        
                        <!-- Social Media -->
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-share-alt me-2"></i>Mạng xã hội
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fab fa-facebook-f me-1"></i> Facebook
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-info">
                                    <i class="fab fa-twitter me-1"></i> Twitter
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger">
                                    <i class="fab fa-youtube me-1"></i> YouTube
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-dark">
                                    <i class="fab fa-tiktok me-1"></i> TikTok
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Map -->
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.434328630865!2d106.686069!3d10.829511!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528e195f816d5%3A0x7e9a8a9b9b9b9b9b!2s12%20Nguy%E1%BB%85n%20V%C4%83n%20B%E1%BA%A3o%2C%20Ph%C6%B0%E1%BB%9Dng%204%2C%20G%C3%B2%20V%E1%BA%A5p%2C%20Th%C3%A0nh%20ph%E1%BB%91%20H%E1%BB%93%20Ch%C3%AD%20Minh!5e0!3m2!1svi!2s!4v1734284800000!5m2!1svi!2s"
                        width="100%" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Bản đồ Khách sạn FunTeam tại 12 Nguyễn Văn Bảo">
                    </iframe>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="contact-form">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-paper-plane me-2"></i>Gửi Tin Nhắn
                    </h4>
                    <p class="text-muted mb-4">Điền thông tin bên dưới, chúng tôi sẽ liên hệ lại sớm nhất.</p>
                    
                    <form id="contactForm">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" placeholder="Nguyễn Văn A" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" placeholder="0900 123 456" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" placeholder="example@email.com" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Chủ đề <span class="text-danger">*</span></label>
                            <select class="form-select" id="subject" required>
                                <option value="" selected disabled>-- Chọn chủ đề --</option>
                                <option value="booking">Đặt phòng/Đổi phòng</option>
                                <option value="support">Hỗ trợ kỹ thuật</option>
                                <option value="feedback">Góp ý dịch vụ</option>
                                <option value="complaint">Khiếu nại</option>
                                <option value="partnership">Hợp tác đối tác</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Nội dung <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" rows="4" 
                                      placeholder="Xin vui lòng mô tả chi tiết yêu cầu của bạn..." 
                                      required></textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agree" required>
                            <label class="form-check-label" for="agree">
                                Tôi đồng ý với <a href="#" class="text-primary">điều khoản</a> và <a href="#" class="text-primary">chính sách bảo mật</a>
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-paper-plane me-2"></i>Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                    
                    <!-- Quick Contact -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-bolt me-2"></i>Liên hệ nhanh
                        </h6>
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="tel:1900123456" class="text-decoration-none text-dark">
                                    <div class="quick-contact-item">
                                        <i class="fas fa-phone fa-lg text-success mb-2"></i>
                                        <p class="mb-0 small fw-bold">Gọi ngay</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="mailto:hotro@funteamhotel.com" class="text-decoration-none text-dark">
                                    <div class="quick-contact-item">
                                        <i class="fas fa-envelope fa-lg text-warning mb-2"></i>
                                        <p class="mb-0 small fw-bold">Email</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="https://zalo.me/0900123456" target="_blank" class="text-decoration-none text-dark">
                                    <div class="quick-contact-item">
                                        <i class="fas fa-comment-dots fa-lg text-primary mb-2"></i>
                                        <p class="mb-0 small fw-bold">Zalo</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="contact-card">
                    <div class="card-body">
                        <h4 class="text-primary mb-3">
                            <i class="fas fa-question-circle me-2"></i>Câu Hỏi Thường Gặp
                        </h4>
                        <div class="accordion faq-accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Làm thế nào để đặt phòng?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Bạn có thể đặt phòng qua: 1) Website, 2) Hotline 1900 123 456, 3) Đến trực tiếp khách sạn.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Có dịch vụ đưa đón sân bay không?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Có, chúng tôi cung cấp dịch vụ đưa đón sân bay Tân Sơn Nhất. Vui lòng đặt trước 24 giờ.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Thời gian check-in/check-out?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Check-in: 14:00, Check-out: 12:00. Check-in sớm/check-out muộn tùy thuộc vào tình trạng phòng.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        Có hủy phòng miễn phí không?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Hủy miễn phí trước 48 giờ. Trong vòng 48 giờ, phí hủy là 50% giá phòng.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="mt-4 pt-3 border-top text-center text-muted">
            <p class="mb-2">
                <strong>Khách sạn FunTeam</strong> 
            </p>
            <p class="small mb-3">
                © 2024 PTUD_FunTeam. Mọi quyền được bảo lưu.
                
            </p>
        </footer>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Form Submission Script -->
    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Lấy giá trị từ form
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const subject = document.getElementById('subject').value;
            
            // Hiển thị thông báo thành công
            const alertHTML = `
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Cảm ơn ${name}!</strong> Tin nhắn của bạn đã được gửi thành công. 
                    Chúng tôi sẽ liên hệ với số ${phone} trong vòng 24 giờ làm việc.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Chèn thông báo trên form
            this.insertAdjacentHTML('beforebegin', alertHTML);
            
            // Reset form
            this.reset();
            
            // Tự động đóng thông báo sau 5 giây
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        });
        
        // Làm mới lại nếu người dùng quay lại trang
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                document.getElementById('contactForm').reset();
            }
        });
    </script>
</body>
</html>