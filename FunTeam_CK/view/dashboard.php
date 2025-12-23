<?php
// view/dashboard.php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION["dn"]) || $_SESSION["dn"] !== true) {
    header("Location: index.php");
    exit();
}

// Kiểm tra role
if (!isset($_SESSION["role"])) {
    echo "Lỗi: Không có role trong session!";
    exit();
}

$role = $_SESSION["role"];

// Load dashboard theo role
switch($role) {
    case 'admin':
        include('dashboard_admin.php');
        break;
    case 'quanly':
        include('dashboard_quanly.php');
        break;
    case 'letan':
        include('dashboard_letan.php');
        break;
    case 'buongphong':
        include('dashboard_buongphong.php');
        break;
    case 'doan':
        include('dashboard_doan.php');
        break;
    case 'khachhang':
        include('dashboard_khachhang.php');
        break;
    default:
        echo "Lỗi: Role không hợp lệ: $role";
        break;
}
?>