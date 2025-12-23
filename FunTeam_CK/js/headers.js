function headerRender() {
    const headerElement = document.querySelector("#header");
    if (headerElement) {
        const urlParams = new URLSearchParams(window.location.search);
        const userName = urlParams.get('user') || 'Người dùng';
        const headerHTML = `
                <div id="header">
                    <div class="row bg-color">
                        <nav class="navbar navbar-expand-sm navbar-drank justify-content-left">
                            <div class="col ps-4">
                                <ul id="ul1" class="navbar-nav">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link text-white">
                                            <span><i class="fas fa-envelope"></i></span>
                                            info.funteam@gmail.com
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link text-white">
                                            <span><i class="fas fa-phone rotate"></i></span>
                                            +12 34.567.888
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col" style="padding-right:30px;">
                                <ul id="ul2" class="navbar-nav">
                                    <li class="nav-item">
                                        <a href="https://www.facebook.com/" class="nav-link pe-2">
                                            <span><i class="fab fa-facebook text-white"></i></span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="https://x.com/?lang=vi" class="nav-link pe-2">
                                            <span><i class="fab fa-twitter text-white"></i></span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="https://www.instagram.com/" class="nav-link pt-2 pe-3">
                                            <span><i class="fab fa-instagram text-white"></i></span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <div class="nav-link text-white">
                                    <span><i class="fas fa-user pe-2"></i>Xin chào, ${userName}</span>
                                </div>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                    <div class="row">
                        <nav class="navbar navbar-expand-sm"  style="background: linear-gradient(135deg, #0d22acff);">
                            <div class="container-fluid">
                                <div class="col ps-3">
                                    <a class="navbar-brand" href="Trangchu.html" style="font-size:15px;">
                                        <img src="../img/logo.jpg" style="width:80px;" class="rounded-circle">
                                    </a>
                                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                                        <span class="navbar-toggler-icon"></span>
                                    </button>
                                </div>
                                <div class="collapse navbar-collapse" id="collapsibleNavbar">

                            <div class="col-md-11">
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
                        </div>
                            </div>
                        </nav>
                    </div>
                </div>
            `;

        headerElement.innerHTML = headerHTML;
    }
}

// Gọi hàm render
document.addEventListener('DOMContentLoaded', function() {
    headerRender();
});