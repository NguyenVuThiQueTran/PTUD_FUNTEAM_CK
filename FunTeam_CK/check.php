<?php
// view/lienhe.php - THÊM DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// DEBUG: Hiển thị thông tin
echo "<!-- DEBUG: File: " . __FILE__ . " -->\n";
echo "<!-- DEBUG: URL: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . " -->\n";
echo "<!-- DEBUG: SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . " -->\n";

// Xác định đường dẫn
$current_file = $_SERVER['PHP_SELF'];
$is_in_view_folder = (strpos($current_file, '/view/') !== false);

if ($is_in_view_folder) {
    $header_path = '../layout/header.php';
    $index_path = '../index.php';
} else {
    $header_path = 'layout/header.php';
    $index_path = 'index.php';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - Khách sạn FunTeam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Rest of your code... -->