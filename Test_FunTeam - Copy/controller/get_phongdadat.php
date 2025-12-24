<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

/* ===== CHECK LOGIN ===== */
if (!isset($_SESSION['email'])) {
    echo json_encode(array());
    exit;
}

$email = $_SESSION['email'];

/* ===== LOAD DATABASE ===== */
require_once dirname(__DIR__) . '/../model/clsconnect.php';

$db = new clsKetNoi();
$conn = $db->moketnoi();

/* ===== SQL ===== */
$sql = "
SELECT 
    dp.maDDP,
    p.soPhong,
    dp.ngayNhanPhong,
    dp.ngayTraPhong,
    dp.trangThai
FROM dondatphong dp
JOIN khachhang kh ON dp.idKH = kh.idKH
JOIN chitietdatphong ct ON dp.maDDP = ct.maDDP
JOIN phong p ON ct.maPhong = p.maPhong
WHERE kh.email = ?
ORDER BY dp.ngayNhanPhong DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

/* ===== PHP 5 SAFE ===== */
mysqli_stmt_bind_result(
    $stmt,
    $maDDP,
    $soPhong,
    $ngayNhan,
    $ngayTra,
    $trangThai
);

$data = array();

while (mysqli_stmt_fetch($stmt)) {
    $data[] = array(
        "maDDP" => $maDDP,
        "soPhong" => $soPhong,
        "ngayNhanPhong" => $ngayNhan,
        "ngayTraPhong" => $ngayTra,
        "trangThai" => $trangThai
    );
}

mysqli_stmt_close($stmt);

echo json_encode($data);
