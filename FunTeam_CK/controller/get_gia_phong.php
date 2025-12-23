<?php
header("Content-Type: application/json; charset=utf-8");

/*
 * KHÔNG dùng __DIR__ vì WAMP 2.0 / PHP 5.x hay lỗi
 * Dùng đường dẫn tương đối trực tiếp
 */
require_once "../model/clsconnect.php";

if (!isset($_GET["maPhong"])) {
    echo json_encode(array(
        "status" => false,
        "message" => "Thiếu mã phòng"
    ));
    exit;
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}
}

$maPhong = $_GET["maPhong"];

$db = new clsKetNoi();
$conn = $db->moketnoi();

$sql = "
SELECT giaPhong
FROM phong
WHERE maPhong = '" . mysqli_real_escape_string($conn, $maPhong) . "'
LIMIT 1
";

$rs = mysqli_query($conn, $sql);

if (!$rs || mysqli_num_rows($rs) == 0) {
    echo json_encode(array(
        "status" => false,
        "message" => "Không tìm thấy phòng"
    ));
    exit;
}

$row = mysqli_fetch_assoc($rs);

echo json_encode(array(
    "status" => true,
    "giaPhong" => (int)$row["giaPhong"]
));
