<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = "";
$success = "";


if (isset($_POST["btnLogin"])) {
    include("../controller/cLogin.php"); 
    $p = new controlUser();
    $email = trim($_POST["txtEmail"]);
    $Pwd = trim($_POST["txtPwd"]);

    $p->cLogin($email, $Pwd);
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập tài khoản</title>

    <link rel="shortcut icon" href="../img/logo.png">

    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/shared.css">
    <link rel="stylesheet" href="../css/login.css">

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="../BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">

    
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }

        .bg-image {
            background-image: url('../img/Nen.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form {
            width: 400px;
            max-width: 90%;
        }

        .bg-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }

        .form-input {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            height: 40px;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }

        .form-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .main-button {
            background-color: #0d6efd;
            color: #fff;
            border: none;
            height: 40px;
            font-weight: 600;
            font-size: 0.9rem;
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

        .alter {
            background: white;
            padding: 0 10px;
            position: relative;
            z-index: 1;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .alter-line {
            border: 0;
            border-top: 1px solid #dee2e6;
            top: 50%;
            z-index: 0;
        }

        .social-btn {
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .social-btn:hover {
            transform: scale(1.1);
        }

        .alert {
            font-size: 0.8rem;
            padding: 0.5rem;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div id="header"></div>
        
        <div class="row bg-image">
            <div class="d-flex align-items-center justify-content-center">
                <div class="form w-30 mx-auto bg-form">
                    <div class="form-header mb-4">
                        <h3 class="text-uppercase text-center fw-semibold mb-3 text-main">Đăng nhập</h3>
                    </div>
                    
                    <?php if($error != ""): ?>
                        <div class="alert alert-danger mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-body mb-4">
                        <form name="frmDangNhap" method="POST" action="#" class="d-flex flex-column gap-2">
                            <div>
                                <input type="email" name="txtEmail" class="form-control form-input rounded-0" 
                                       placeholder="Email" required 
                                       value="<?php echo isset($_POST['txtEmail']) ? htmlspecialchars($_POST['txtEmail']) : ''; ?>">
                            </div>
                            <div>
                                <input type="password" name="txtPwd" class="form-control form-input rounded-0" 
                                       placeholder="Mật khẩu" required>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Nhớ thông tin đăng nhập</label>
                            </div>
                            <button type="submit" name="btnLogin" class="main-button fw-semibold text-20 mb-3">Đăng nhập</button>
                        </form>
                    </div>
                    
                    <div class="form-footer">
                        <p class="text-main text-center fw-semibold" style="cursor: pointer;" 
                        onclick="window.location.href='../view/quenmatkhau.php'">
                            Quên mật khẩu
                        </p>
                        
                        <p class="fw-semibold text-center">Bạn chưa có tài khoản 
                            <a href="../view/dangky_khachhang.php" class="text-main">Đăng ký ngay</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row bg-color" id="footer">
            <script src="../layout/footer.php" type="module"></script>
        </div>
    </div>

    <script src="../js/shared.js" type="module"></script>
    
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('input[name="txtEmail"]').focus();
        });

        document.forms['frmDangNhap'].addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="txtEmail"]').value;
            const password = document.querySelector('input[name="txtPwd"]').value;
            let isValid = true;

            if (!email) {
                isValid = false;
            } else if (!validateEmail(email)) {
                isValid = false;
            }

            if (!password) {
                isValid = false;
            }

            if (!isValid && !email && !password) {
                e.preventDefault();
                alert('Vui lòng nhập đầy đủ thông tin!');
            }
        });

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    </script>
</body>
</html>