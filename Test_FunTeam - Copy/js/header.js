// Render header
function headerRender() {
    const headerElement = document.querySelector("#header");
    if (headerElement) {
        const headerHTML = `
            
            <div class="row">
                <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #0d22acff);">
                    <div class="container-fluid">
                        <div class="col-auto ps-2">
                            <a class="navbar-brand" href="Trangchu.html">
                                <img src="../img/logo.jpg" style="width:90px;"class="rounded-circle" alt="Logo">
                            </a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                        </div>
                        
                        <div class="collapse navbar-collapse" id="collapsibleNavbar">

                            <div class="col-md-9">
                                <ul class="navbar-nav justify-content-center w-100">
                                    <li class="nav-item mx-2">
                                        <a href="Trangchu.html" class="nav-link text-white">
                                            <i class="fas fa-home me-1 fs-4 px-3"></i>Trang Chủ
                                        </a>
                                    </li>
                                    <li class="nav-item mx-2">
                                        <a href="#" class="nav-link text-white">
                                            <i class="fas fa-concierge-bell me-1 fs-4 px-3"></i>Dịch Vụ
                                        </a>
                                    </li>
                                    <li class="nav-item mx-2">
                                        <a href="#" class="nav-link text-white">
                                            <i class="fas fa-gift me-1 fs-4 px-3"></i>Khuyến Mãi
                                        </a>
                                    </li>
                                    <li class="nav-item mx-2">
                                        <a href="#" class="nav-link text-white">
                                            <i class="fas fa-images me-1 fs-4 px-4"></i>Hình Ảnh
                                        </a>
                                    </li>
                                    <li class="nav-item mx-2">
                                        <a href="#" class="nav-link text-white">
                                            <i class="fas fa-map-marker-alt me-1 fs-4 px-3"></i>Vị Trí
                                        </a>
                                    </li>
                                    <li class="nav-item mx-2">
                                        <a href="#" class="nav-link text-white">
                                            <i class="fas fa-phone-alt me-1 fs-4 px-3"></i>Liên Hệ
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="col" style="padding-right:30px;">
                        <ul id="ul2" class="navbar-nav justify-content-end">
                            
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-white"><i class="fas fa-user pe-2"></i></span>
                                    <a href="login.php" class="text-white text-decoration-none">Đăng nhập |</a>
                                    <a href="register.php" class="text-white text-decoration-none">Đăng ký</a>
                                </div>
                            </li>
                        </ul>
                    </div>


                        </div>
                    </div>
                </nav>
            </div>
        `;

        headerElement.innerHTML = headerHTML;
    }
}

// Gọi hàm render
document.addEventListener('DOMContentLoaded', function() {
    headerRender();
});