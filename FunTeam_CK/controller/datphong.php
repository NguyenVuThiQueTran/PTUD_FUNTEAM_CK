<?php
header("Content-Type: application/json; charset=utf-8");
session_start();

/* ===== CHECK LOGIN ===== */
if (!isset($_SESSION['email'])) {
    echo json_encode(array(
        "status" => false,
        "message" => "Chưa đăng nhập"
    ));
    exit;
}

/* ===== LOAD DATABASE ===== */
require_once dirname(__FILE__) . '/../model/clsconnect.php';

$db   = new clsKetNoi();
$conn = $db->moketnoi();

/* ===== READ JSON ===== */
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(array(
        "status" => false,
        "message" => "JSON không hợp lệ"
    ));
    exit;
}

/* ===== DATA ===== */
$maPhong  = mysqli_real_escape_string($conn, $data["maPhong"]);
$hoTen    = trim($data["hoTen"]);
$sdt      = trim($data["sdt"]);
$cccd     = trim($data["cccd"]);
$ngayNhan = $data["ngayNhan"];
$ngayTra  = $data["ngayTra"];
$email    = $_SESSION['email'];

/* ===== VALIDATE ===== */
if (
    strlen($hoTen) < 3 ||
    !preg_match("/^[0-9]{10}$/", $sdt) ||
    !preg_match("/^[0-9]{12}$/", $cccd)
) {
    echo json_encode(array(
        "status" => false,
        "message" => "Thông tin không hợp lệ"
    ));
    exit;
}

/* ===== LẤY idKH THEO EMAIL SESSION ===== */
$rsKH = mysqli_query($conn, "
    SELECT idKH FROM khachhang WHERE email = '$email' LIMIT 1
");

if (!$rsKH || mysqli_num_rows($rsKH) == 0) {
    echo json_encode(array(
        "status" => false,
        "message" => "Không tìm thấy khách hàng"
    ));
    exit;
}

$rowKH = mysqli_fetch_assoc($rsKH);
$idKH  = $rowKH["idKH"];

/* ===== CHECK PHÒNG TRỐNG ===== */
$rsPhong = mysqli_query($conn, "
    SELECT tinhTrang FROM phong WHERE maPhong='$maPhong' LIMIT 1
");

$rowPhong = mysqli_fetch_assoc($rsPhong);
if ($rowPhong["tinhTrang"] != "Trống") {
    echo json_encode(array(
        "status" => false,
        "message" => "Phòng không còn trống"
    ));
    exit;
}

/* ===== TẠO MÃ ĐƠN ===== */
$maDDP = "DDP" . time();

/* ===== TRANSACTION (PHP 5) ===== */
mysqli_autocommit($conn, false);

try {

    /* INSERT ĐƠN */
    mysqli_query($conn, "
        INSERT INTO dondatphong(
            maDDP,idKH,ngayDatPhong,
            ngayNhanPhong,ngayTraPhong,trangThai
        ) VALUES (
            '$maDDP',$idKH,CURDATE(),
            '$ngayNhan','$ngayTra','DangCho'
        )
    ");

    /* INSERT CHI TIẾT */
    mysqli_query($conn, "
        INSERT INTO chitietdatphong(maDDP,maPhong)
        VALUES('$maDDP','$maPhong')
    ");

    /* UPDATE PHÒNG */
    mysqli_query($conn, "
        UPDATE phong SET tinhTrang='Đã đặt'
        WHERE maPhong='$maPhong'
    ");

    mysqli_commit($conn);

    echo json_encode(array(
        "status" => true,
        "message" => "Đặt phòng thành công"
    ));

} catch (Exception $e) {

    mysqli_rollback($conn);

    echo json_encode(array(
        "status" => false,
        "message" => "Lỗi đặt phòng"
    ));
}

mysqli_autocommit($conn, true);