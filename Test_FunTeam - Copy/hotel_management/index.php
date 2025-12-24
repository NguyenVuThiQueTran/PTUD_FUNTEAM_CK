<?php
session_start();
require_once 'config/database.php';

// Xử lý đăng nhập
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Kiểm tra tài khoản
        $stmt = $conn->prepare("SELECT * FROM taikhoan WHERE email = :email AND trangThai = 'HoatDong'");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Kiểm tra mật khẩu (Hỗ trợ cả PHP cũ và mới)
            $checkPass = false;
            
            // 1. Kiểm tra MD5
            if (md5($password) === $user['matKhau']) {
                $checkPass = true;
            } 
            // 2. Kiểm tra mật khẩu thường
            elseif ($password === $user['matKhau']) {
                $checkPass = true;
            }
            // 3. Kiểm tra hàm hash mới
            elseif (function_exists('password_verify') && password_verify($password, $user['matKhau'])) {
                $checkPass = true;
            }

            if ($checkPass) {
                // Lưu session (Dùng array() cho PHP cũ)
                $_SESSION['user'] = array(
                    'id' => $user['idTK'],
                    'email' => $user['email'],
                    'vaiTro' => $user['vaiTro']
                );

                // Lấy tên hiển thị
                if ($user['loaiTaiKhoan'] == 'NhanVien') {
                    $stmtInfo = $conn->prepare("SELECT hoTen FROM nhansu WHERE idUser = :id");
                    $stmtInfo->execute(array(':id' => $user['idTK']));
                    $info = $stmtInfo->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['user']['hoTen'] = $info ? $info['hoTen'] : 'Admin';
                } else {
                    $stmtInfo = $conn->prepare("SELECT hoTen FROM khachhang WHERE email = :email");
                    $stmtInfo->execute(array(':email' => $user['email']));
                    $info = $stmtInfo->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['user']['hoTen'] = $info ? $info['hoTen'] : 'Khách hàng';
                }

                // Chuyển hướng
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Mật khẩu không chính xác!";
            }
        } else {
            $error = "Tài khoản không tồn tại hoặc bị khóa!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* Hình nền full màn hình */
            background: url('img/Nen.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Lớp phủ mờ */
        .overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.1); 
            z-index: 1;
        }

        /* Hộp đăng nhập */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 10px;
            width: 420px; /* Tăng chiều rộng một chút cho thoáng */
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
            z-index: 2;
            position: relative;
        }

        .login-title {
            color: #3b71ca;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 30px;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }

        /* Input */
        .form-control {
            background-color: #e8f0fe;
            border: 1px solid #ced4da;
            padding: 12px;
            font-size: 0.95rem;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .form-control:focus {
            background-color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(59, 113, 202, 0.25);
            border-color: #3b71ca;
        }

        /* Button */
        .btn-login {
            background-color: #3b71ca;
            color: white;
            font-weight: 600;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 5px;
            transition: background 0.3s;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .btn-login:hover {
            background-color: #2a5298;
        }

        .alert-error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-bottom: 20px;
            display: block;
            font-weight: bold;
        }

        /* Style cho dòng thông báo mới thêm */
        .note-text {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 20px;
            font-style: italic;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
    </style>
</head>
<body>

    <div class="overlay"></div>

    <div class="login-card">
        <h3 class="login-title">Đăng Nhập</h3>
        
        <?php if($error): ?>
            <span class="alert-error"><?php echo $error; ?></span>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <input type="text" name="email" class="form-control" placeholder="Tên đăng nhập / Email" required>
            </div>
            
            <div class="mb-4">
                <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
            </div>

            <button type="submit" class="btn btn-login">Đăng nhập</button>
            
            <div class="note-text">
                Nếu quên tên đăng nhập hoặc mật khẩu vui lòng liên hệ phòng quản lý để được cấp lại
            </div>
        </form>
    </div>

</body>
</html>