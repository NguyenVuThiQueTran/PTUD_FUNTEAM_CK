hotel_management/
│
├── index.php                 # Trang đăng nhập
├── dashboard.php             # Trang chính sau đăng nhập
├── logout.php                # Đăng xuất
│
├── config/
│   └── database.php          # Kết nối cơ sở dữ liệu
│
├── controller/
│   ├── authController.php    # Xử lý đăng nhập/đăng xuất
│   ├── phongController.php   # Quản lý phòng
│   ├── nhanvienController.php # Quản lý nhân sự
│   ├── khachhangController.php # Quản lý khách hàng
│   └── dichvuController.php   # Quản lý dịch vụ
│
├── model/
│   ├── PhongModel.php
│   ├── NhanVienModel.php
│   ├── KhachHangModel.php
│   └── DichVuModel.php
│
├── view/
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── quanlyphong.php
│   ├── quanlynhanvien.php
│   ├── quanlykhachhang.php
│   └── quanlydichvu.php
│
├── assets/
│   ├── css/
|   |   ├── bootstrap.min.css
│   |── js/
|   |   ├── bootstrap.bundle.min.js
