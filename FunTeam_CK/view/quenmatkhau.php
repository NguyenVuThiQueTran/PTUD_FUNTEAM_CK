<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../model/clsQMK.php");

$error = "";
$success = "";

if(isset($_POST['btnForgotPassword'])){
    $email = trim($_POST['email']);
    
    if($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email không đúng định dạng!";
        } else {
            $qmk = new clsQMK();
            $check = $qmk->kiemTraEmail($email);
            
            if(!$check){
                $error = "Email không tồn tại trong hệ thống!";
            } else {
                $result = $qmk->guiMatKhauMoi($email);

                if(isset($result['status']) && $result['status'] && isset($result['newpass'])){
                    $success = "Mật khẩu mới: " . $result['newpass'];
                } else {
                    $error = $result['message'];
                }
            }
        }
    } else {
        $error = "Vui lòng nhập email!";
    }
}
$base_path = '/PTUD_FunTeam-main';
?>

<?php if (isset($_GET['success'])) { ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php } ?>

<?php if (isset($_GET['error'])) { ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php } ?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('<?php echo $base_path; ?>/img/Nen.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .forgot-card {
            width: 400px;
            max-width: 90%;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            border: none;
            background: rgba(255, 255, 255, 0.95);
        }
        .forgot-card .card-body {
            padding: 2rem;
        }
        .form-control {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            height: 45px;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .main-button {
            background-color: #0d6efd;
            color: #fff;
            border: none;
            height: 45px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .main-button:hover {
            background-color: #0b5ed7;
        }
        .text-main {
            color: #0d6efd !important;
            text-decoration: none;
        }
        .text-main:hover {
            text-decoration: underline;
        }
        .alert {
            font-size: 0.9rem;
            padding: 0.75rem;
            border-radius: 5px;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="card forgot-card">
        <div class="card-body">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock-fill text-main" style="font-size: 3rem;"></i>
                <h3 class="fw-semibold mt-2">Quên mật khẩu</h3>
                <p class="text-muted">Nhập email để lấy lại mật khẩu</p>
            </div>

            <div class="info-box">
                <i class="bi bi-info-circle-fill text-main"></i>
                <small>Hệ thống sẽ tạo mật khẩu mới cho tài khoản của bạn.</small>
            </div>

            <?php if($error != ""): ?>
                <div class="alert alert-danger mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if($success != ""): ?>
                <div class="alert alert-success mb-3" role="alert">
                    <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="d-flex flex-column" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label fw-medium">Email đăng ký</label>
                    <input type="email" name="email" class="form-control" placeholder="Nhập email của bạn" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <button type="submit" name="btnForgotPassword" class="main-button">
                    <i class="bi bi-send"></i> Gửi mật khẩu mới
                </button>
            </form>

            <div class="text-center mt-3">
                <p class="mb-2">
                    <a href="login.php" class="text-main">
                        <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
                    </a>
                </p>
                <p class="mb-0">
                    Chưa có tài khoản? 
                    <a href="dangky_khachhang.php" class="text-main">Đăng ký ngay</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('input[name="email"]').focus();
        });
    </script>
</body>
</html>