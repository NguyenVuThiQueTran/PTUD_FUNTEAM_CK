<?php
header("Content-Type: application/json; charset=utf-8");
session_start();

if (!isset($_SESSION['email'])) {
    echo json_encode(array(
        "status" => false,
        "message" => "Chưa đăng nhập"
    ));
    exit;
}

require_once dirname(__FILE__) . '/../model/clsconnect.php';

$db   = new clsKetNoi();
$conn = $db->moketnoi();
$email = $_SESSION['email'];

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(array(
        "status" => false,
        "message" => "JSON không hợp lệ"
    ));
    exit;
}

$type = $data["type"];

/* ===== TRANSACTION ===== */
mysqli_autocommit($conn, false);

try {

    /* ===== HỦY 1 ===== */
    if ($type == "one") {

        $maDDP = $data["maDDP"];

        /* CHECK ĐÃ NHẬN */
        $rs = mysqli_query($conn, "
            SELECT trangThai FROM dondatphong
            WHERE maDDP='$maDDP'
        ");
        $r = mysqli_fetch_assoc($rs);

        if ($r["trangThai"] == "DaNhan") {
            throw new Exception("Phòng đã nhận, không thể hủy");
        }

        /* UPDATE ĐƠN */
        mysqli_query($conn,"
            UPDATE dondatphong
            SET trangThai='DaHuy'
            WHERE maDDP='$maDDP'
        ");

        /* TRẢ PHÒNG */
        mysqli_query($conn,"
            UPDATE phong p
            JOIN chitietdatphong c ON p.maPhong=c.maPhong
            SET p.tinhTrang='Trống'
            WHERE c.maDDP='$maDDP'
        ");
    }

    /* ===== HỦY TẤT CẢ ===== */
    if ($type == "all") {

        $rs = mysqli_query($conn,"
            SELECT COUNT(*) c FROM dondatphong dp
            JOIN khachhang kh ON dp.idKH=kh.idKH
            WHERE kh.email='$email'
            AND dp.trangThai='DaNhan'
        ");
        $r = mysqli_fetch_assoc($rs);

        if ($r["c"] > 0) {
            throw new Exception("Có phòng đã nhận");
        }

        mysqli_query($conn,"
            UPDATE dondatphong dp
            JOIN khachhang kh ON dp.idKH=kh.idKH
            SET dp.trangThai='DaHuy'
            WHERE kh.email='$email'
        ");

        mysqli_query($conn,"
            UPDATE phong
            SET tinhTrang='Trống'
            WHERE maPhong IN (
                SELECT maPhong FROM chitietdatphong
            )
        ");
    }

    mysqli_commit($conn);

    echo json_encode(array(
        "status" => true,
        "message" => "Hủy thành công"
    ));

} catch (Exception $e) {

    mysqli_rollback($conn);

    echo json_encode(array(
        "status" => false,
        "message" => $e->getMessage()
    ));
}

mysqli_autocommit($conn, true);
