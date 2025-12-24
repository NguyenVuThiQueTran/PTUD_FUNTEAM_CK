<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_path = '/PTUD_FunTeam-main';
$error = '';
$success = '';

if(isset($_POST['btnDangKy'])){
    $hoTen = trim($_POST['ho_ten']);
    $email = trim($_POST['email']);
    $soDienThoai = trim($_POST['so_dien_thoai']);
    $cccd = trim($_POST['cccd']);
    $diaChi = trim($_POST['dia_chi']);
    $matKhau = trim($_POST['mat_khau']);
    $xacNhanMatKhau = trim($_POST['xac_nhan_mk']);

    require_once("../controller/cKhachHang.php");
    
    $ctrl = new cKhachHang();
    $ketQua = $ctrl->dangKyKhachHang($hoTen, $email, $soDienThoai, $cccd, $diaChi, $matKhau, $xacNhanMatKhau);
    
    if($ketQua['success']){
        $success = $ketQua['message'];

        echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 3000);
              </script>";
    } else {
        $error = $ketQua['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng ký</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
body, html { height: 100%; margin: 0; }
body { display: flex; align-items: center; justify-content: center; 
       background-image: url('<?php echo $base_path; ?>/img/Nen.jpg'); 
       background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed; }
.register-card { width: 400px; max-width: 90%; border-radius: 10px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); border: none; background: rgba(255, 255, 255, 0.95); padding: 2rem; }
.form-control { background-color: #f8f9fa; border: 1px solid #dee2e6; height: 45px; padding: 0.5rem 0.75rem; font-size: 0.9rem; margin-bottom: 1rem; }
.form-control:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); }
.main-button { background-color: #0d6efd; color: #fff; border: none; height: 45px; font-weight: 600; font-size: 1rem; width: 100%; border-radius: 5px; transition: background-color 0.3s; }
.main-button:hover { background-color: #0b5ed7; }
.alert { font-size: 0.9rem; padding: 0.75rem; border-radius: 5px; margin-bottom: 1rem; }
</style>
</head>
<body>

<div class="register-card">
    <div class="text-center mb-4">
        <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
        <h4 class="fw-semibold mt-2">Đăng ký tài khoản</h4>
        <p class="text-muted">Tạo tài khoản khách hàng mới</p>
    </div>

    <?php if($error != ""): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if($success != ""): ?>
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="ho_ten" class="form-control" placeholder="Họ và tên" required 
               value="<?php echo isset($_POST['ho_ten']) ? htmlspecialchars($_POST['ho_ten']) : ''; ?>">
        <input type="email" name="email" class="form-control" placeholder="Email" required
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <input type="text" name="so_dien_thoai" class="form-control" placeholder="Số điện thoại" required
               value="<?php echo isset($_POST['so_dien_thoai']) ? htmlspecialchars($_POST['so_dien_thoai']) : ''; ?>">
        <input type="text" name="cccd" class="form-control" placeholder="CCCD" required
               value="<?php echo isset($_POST['cccd']) ? htmlspecialchars($_POST['cccd']) : ''; ?>">
        <input type="text" name="dia_chi" class="form-control" placeholder="Địa chỉ" required
               value="<?php echo isset($_POST['dia_chi']) ? htmlspecialchars($_POST['dia_chi']) : ''; ?>">
        <input type="password" name="mat_khau" class="form-control" placeholder="Mật khẩu" required>
        <input type="password" name="xac_nhan_mk" class="form-control" placeholder="Xác nhận mật khẩu" required>

        <button type="submit" name="btnDangKy" class="main-button">
            <i class="bi bi-person-plus"></i> Đăng ký
        </button>
    </form>

    <div class="text-center mt-3">
        <p class="mb-0">
            Đã có tài khoản? 
            <a href="login.php" class="text-primary text-decoration-none fw-semibold">Đăng nhập ngay</a>
        </p>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('input[name="ho_ten"]').focus();
});
</script>

</body>
</html>
