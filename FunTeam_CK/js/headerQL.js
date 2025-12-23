// js/header.js
function headerRender() {
    const headerElement = document.querySelector("#header");
    if (headerElement) {
        const headerHTML = `
            <!-- Thanh màu xám trên cùng -->
            <div class="row bg-dark py-2 text-white">
                <div class="col d-flex justify-content-between align-items-center px-4 flex-wrap">
                    <div class="contact-info mb-2 mb-md-0">
                        <i class="fas fa-envelope me-2"></i> info.funteam@gmail.com
                        <span class="mx-3 d-none d-md-inline">|</span>
                        <i class="fas fa-phone-alt me-2"></i> +12 34.567.888
                    </div>
                    <div class="d-flex align-items-center flex-wrap justify-content-center">
                        <a href="https://www.facebook.com/" class="text-white me-3 social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://x.com/?lang=vi" class="text-white me-3 social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.instagram.com/" class="text-white me-3 social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-white me-4 social-icon">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <div class="user-section d-flex align-items-center">
                            <i class="fas fa-user me-2"></i>
                            <span id="user-name">Xin chào, Người dùng</span>
                            <button id="logout-btn" class="btn btn-sm btn-outline-light ms-3">
                                <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thanh điều hướng màu xanh -->
            <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d47a1;">
                <div class="container-fluid px-4">
                    <!-- Logo -->
                    <a class="navbar-brand d-flex align-items-center" href="Trangchu.html">
                        <img src="../img/logo.jpg" alt="Logo" class="rounded-circle me-2" style="width:60px; height:60px;">
                        <span class="fw-bold">FUNTEAM</span>
                    </a>

                    <!-- Nút thu gọn -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Menu -->
                    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                        <ul class="navbar-nav text-center">
                            <li class="nav-item mx-2">
                                <a href="Trangchu.html" class="nav-link active">
                                    <i class="fas fa-home me-2"></i>Trang Chủ
                                </a>
                            </li>
                            <li class="nav-item mx-2">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-concierge-bell me-2"></i>Dịch Vụ
                                </a>
                            </li>
                            <li class="nav-item mx-2">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-gift me-2"></i>Khuyến Mãi
                                </a>
                            </li>
                            <li class="nav-item mx-2">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-images me-2"></i>Hình Ảnh
                                </a>
                            </li>
                            <li class="nav-item mx-2">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-map-marker-alt me-2"></i>Vị Trí
                                </a>
                            </li>
                            <li class="nav-item mx-2">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-phone-alt me-2"></i>Liên Hệ
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        `;
        headerElement.innerHTML = headerHTML;

        // Xử lý nút đăng xuất
        const logoutBtn = document.getElementById("logout-btn");
        logoutBtn.addEventListener("click", function () {
            alert("Bạn đã đăng xuất thành công!");
            // Sau này bạn có thể thay alert bằng: window.location.href = "login.html";
        });
    }
}

// Gọi hàm render
document.addEventListener('DOMContentLoaded', headerRender);
