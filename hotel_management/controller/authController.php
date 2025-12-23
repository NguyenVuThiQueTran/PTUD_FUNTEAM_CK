<?php
session_start();
require_once dirname(__FILE__) . '/../config/database.php';

if(isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Thiết lập kết nối
    $database = new Database();
    $conn = $database->getConnection();

    // 1) Thử users
    $user = false;
    $source = null;
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
        $stmt->execute(array($username));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) $source = 'users';
    } catch (PDOException $e) {
        $user = false;
    }

    // 2) Fallback taikhoan
    if (!$user) {
        try {
            $stmt = $conn->prepare("SELECT * FROM taikhoan WHERE email=? LIMIT 1");
            $stmt->execute(array($username));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) $source = 'taikhoan';
        } catch (PDOException $e) {
            $user = false;
        }
    }

    // 3) Fallback khachhang
    if (!$user) {
        try {
            $stmt = $conn->prepare("SELECT * FROM khachhang WHERE email=? LIMIT 1");
            $stmt->execute(array($username));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) $source = 'khachhang';
        } catch (PDOException $e) {
            $user = false;
        }
    }

    // 4) Fallback doan
    if (!$user) {
        try {
            $stmt = $conn->prepare("SELECT * FROM doan WHERE email=? LIMIT 1");
            $stmt->execute(array($username));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) $source = 'doan';
        } catch (PDOException $e) {
            $user = false;
        }
    }

    if($user){
        $pwField = isset($user['password']) ? 'password' : (isset($user['MatKhau']) ? 'MatKhau' : (isset($user['matKhau']) ? 'matKhau' : null));
        $hashed = $pwField ? $user[$pwField] : '';

        $verified = false;
        if ($hashed !== '') {
            if (function_exists('password_verify') && password_verify($password, $hashed)) {
                $verified = true;
            } elseif ($hashed === md5($password) || $hashed === sha1($password) || $hashed === $password) {
                $verified = true;
            }
        }

        if($verified){
            $_SESSION['user'] = $user;
            $_SESSION['user']['_source'] = $source;
            header("Location: ../dashboard.php");
            exit;
        }
    }

    $_SESSION['error'] = "Tài khoản hoặc mật khẩu không đúng!";
    header("Location: ../login.php");
    exit;
} else {
    header("Location: ../login.php");
    exit;
}
?>
