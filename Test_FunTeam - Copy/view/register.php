<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = "";
$success = "";
$base_path = '/PTUD_FunTeam-main';

// Đoạn code xử lý đăng ký
if(isset($_POST['btnDangKy'])){
    $ten_doan = trim($_POST['ten_doan']);
    $truong_doan = trim($_POST['truong_doan']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $so_luong_thanh_vien = intval($_POST['so_luong_thanh_vien']);
    
    if(isset($_POST['thanh_vien'])) {
        $thanh_vien = $_POST['thanh_vien'];
    } else {
        $thanh_vien = array();
    }

    if($ten_doan == "" || $truong_doan == "" || $so_dien_thoai == "" || $so_luong_thanh_vien < 1){
        $error = "Vui lòng điền đầy đủ thông tin.";
    } else {
        include("../controller/cuser.php");
        include("../controller/cdoan.php");

        $user = new cUser();
        $doan = new cDoan();

        // Tạo mã đoàn ngẫu nhiên
        $maDoan = "D".rand(1000,9999);

        // Thêm đoàn vào DB
        $themDoan = $doan->themDoan($maDoan, $ten_doan, $truong_doan, $so_luong_thanh_vien, $so_dien_thoai);
        if(!$themDoan){
            $error = "Lỗi khi thêm đoàn!";
        } else {
            $successCount = 0;
            foreach($thanh_vien as $tv){
                $hoTen = trim($tv['ho_ten']);
                $gmail = trim($tv['gmail']);
                $cccd = trim($tv['cccd']);
                $password = substr(md5(time().$gmail), 0, 8);

                // Tạo tài khoản khách hàng + lưu vào KhachHang
                $idKH = $user->cRegister($gmail, $password, $hoTen, $cccd);

                if($idKH){
                    // Lưu vào bảng ThanhVienDoan
                    $doan->themThanhVienDoan($maDoan, $idKH);
                    $successCount++;
                    // Gửi email mật khẩu (nếu muốn)
                    // mail($gmail, "Mật khẩu đăng nhập", "Mật khẩu: $password");
                }
            }

            if($successCount == $so_luong_thanh_vien){
                $success = "Đăng ký thành công $successCount thành viên! Mật khẩu đã gửi tới email.";
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 3000);
                      </script>";
            } else {
                $error = "Có lỗi khi lưu một số thành viên.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng ký đoàn</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
body, html { 
    height: 100%; 
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
body {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
    background-image: url('<?php echo $base_path; ?>/img/Nen.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}
.login-card {
    width: 100%;
    max-width: 500px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border: none;
    background: rgba(255,255,255,0.98);
    overflow: hidden;
}
.login-card .card-body {
    padding: 2rem;
}
.form-control {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    height: 45px;
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
    border-radius: 8px;
    transition: all 0.3s;
}
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.25);
    background-color: #fff;
}
.main-button {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    color: #fff;
    border: none;
    height: 45px;
    font-weight: 600;
    font-size: 1rem;
    width: 100%;
    border-radius: 8px;
    transition: all 0.3s;
    margin-top: 10px;
}
.main-button:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13,110,253,0.3);
}
.form-footer {
    font-size: 0.9rem;
    margin-top: 1.5rem;
    text-align: center;
}
.text-main {
    color: #0d6efd !important;
    text-decoration: none;
    font-weight: 600;
}
.text-main:hover {
    text-decoration: underline;
    color: #0a58ca !important;
}
.alert {
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border: none;
}
.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}
.alert-success {
    background-color: #d1e7dd;
    color: #0f5132;
    border-left: 4px solid #198754;
}
.member-section {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #f8f9fa;
    transition: all 0.3s;
}
.member-section:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 10px rgba(13,110,253,0.1);
}
.member-header {
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: #495057;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #0d6efd;
}
.login-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
}
.login-header h4 {
    color: #333;
    font-weight: 700;
}
.section-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin-top: 1rem;
    margin-bottom: 0.75rem;
}
.form-group {
    margin-bottom: 1rem;
}
.form-group label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.3rem;
    display: block;
    font-size: 0.9rem;
}
</style>
<script>
function taoInputThanhVien() {
    let container = document.getElementById('khung_thanh_vien');
    container.innerHTML = "";
    let soLuong = parseInt(document.getElementById('so_luong_thanh_vien').value);
    
    if (soLuong > 0) {
        container.innerHTML = '<div class="section-title">Thông tin thành viên</div>';
        
        for(let i = 1; i <= soLuong; i++){
            let div = document.createElement('div');
            div.className = "member-section";
            div.innerHTML = `
                <div class="member-header">Thành viên ${i}</div>
                <div class="mb-2">
                    <label>Họ tên *</label>
                    <input type="text" name="thanh_vien[${i}][ho_ten]" class="form-control" placeholder="Nhập họ tên" required>
                </div>
                <div class="mb-2">
                    <label>Email *</label>
                    <input type="email" name="thanh_vien[${i}][gmail]" class="form-control" placeholder="Nhập email" required>
                </div>
                <div>
                    <label>Số CCCD *</label>
                    <input type="text" name="thanh_vien[${i}][cccd]" class="form-control" placeholder="Nhập số CCCD" required>
                </div>`;
            container.appendChild(div);
        }
    }
}

document.addEventListener('DOMContentLoaded', function(){
    taoInputThanhVien();
    document.querySelector('input[name="ten_doan"]').focus();
});
</script>
</head>
<body>
<div class="card login-card">
    <div class="card-body">
        <div class="login-header text-center">
            <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold mb-0">
                <i class="bi bi-people-fill me-2"></i>Đăng ký đoàn du lịch
            </h4>
            <a href="dashboard.php" class="btn btn-danger btn-sm rounded-circle d-flex align-items-center justify-content-center" 
       style="width: 36px; height: 36px; padding: 0; font-weight: bold; font-size: 1.2rem; text-decoration: none;"
       title="Đóng">
        X
    </a>
        </div>
        </div>

        <?php if($error != ""): ?>
        <div class="alert alert-danger mb-3" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if($success != ""): ?>
        <div class="alert alert-success mb-3" role="alert">
            <i class="bi bi-check-circle me-2"></i> <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="d-flex flex-column" style="gap:1rem;">
            <div class="form-group">
                <label>Tên đoàn *</label>
                <input type="text" name="ten_doan" class="form-control" placeholder="Nhập tên đoàn" required 
                       value="<?php echo isset($_POST['ten_doan']) ? htmlspecialchars($_POST['ten_doan']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Trưởng đoàn *</label>
                <input type="text" name="truong_doan" class="form-control" placeholder="Nhập tên trưởng đoàn" required 
                       value="<?php echo isset($_POST['truong_doan']) ? htmlspecialchars($_POST['truong_doan']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Số điện thoại *</label>
                <input type="text" name="so_dien_thoai" class="form-control" placeholder="Nhập số điện thoại" required 
                       value="<?php echo isset($_POST['so_dien_thoai']) ? htmlspecialchars($_POST['so_dien_thoai']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Số lượng thành viên *</label>
                <input type="number" name="so_luong_thanh_vien" id="so_luong_thanh_vien" class="form-control" 
                       min="1" value="1" required oninput="taoInputThanhVien()" placeholder="Nhập số lượng thành viên">
            </div>

            <div id="khung_thanh_vien"></div>
            
            <button type="submit" name="btnDangKy" class="main-button">
                <i class="bi bi-check-circle me-2"></i>Đăng ký đoàn
            </button>
        </form>

        <div class="form-footer">
            <p class="fw-semibold mb-0">Bạn đã có tài khoản? 
                <a href="login.php" class="text-main">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập ngay
                </a>
            </p>
        </div>
    </div>
</div>

<script>
// Tự động cuộn khi có nhiều thành viên
document.getElementById('so_luong_thanh_vien').addEventListener('input', function() {
    if (this.value > 3) {
        setTimeout(() => {
            window.scrollTo({
                top: document.querySelector('.login-card').offsetTop - 20,
                behavior: 'smooth'
            });
        }, 300);
    }
});
</script>
</body>
</html>