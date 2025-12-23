<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once("../model/clsconnect.php");

$db   = new clsKetNoi();
$conn = $db->moketnoi();

/* =========================
   XUẤT EXCEL
========================= */
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $nam = intval($_GET['nam']);

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=bao_cao_$nam.xls");

    echo "Tháng\tDoanh thu\n";

    $sql = "
    SELECT MONTH(ngayLap) AS thang, SUM(tongTien) AS tong
    FROM hoadon
    WHERE trangThai='DaThanhToan'
    AND YEAR(ngayLap) = $nam
    GROUP BY MONTH(ngayLap)
    ";

    $rs = mysqli_query($conn, $sql);
    while ($r = mysqli_fetch_assoc($rs)) {
        echo $r['thang'] . "\t" . $r['tong'] . "\n";
    }
    exit;
}

/* =========================
   API JSON
========================= */
header("Content-Type: application/json; charset=utf-8");

$nam1 = isset($_GET['nam1']) ? intval($_GET['nam1']) : date("Y");
$nam2 = isset($_GET['nam2']) ? intval($_GET['nam2']) : 0;

/* ===== DOANH THU ===== */
function doanhThuTheoNam($conn, $nam) {
    $data = array();
    for ($i=1; $i<=12; $i++) $data[$i] = 0;

    $sql = "
    SELECT MONTH(ngayLap) AS thang, SUM(tongTien) AS tong
    FROM hoadon
    WHERE trangThai='DaThanhToan'
    AND YEAR(ngayLap) = $nam
    GROUP BY MONTH(ngayLap)
    ";
    $rs = mysqli_query($conn, $sql);
    while ($r = mysqli_fetch_assoc($rs)) {
        $data[intval($r['thang'])] = floatval($r['tong']);
    }
    return array_values($data);
}

$dt1 = doanhThuTheoNam($conn, $nam1);
$dt2 = ($nam2 > 0) ? doanhThuTheoNam($conn, $nam2) : array();

/* ===== KHÁCH HÀNG ===== */
$sqlKH = "
SELECT COUNT(DISTINCT idKH) AS total
FROM dondatphong
WHERE YEAR(ngayDatPhong) = $nam1
";
$kh = mysqli_fetch_assoc(mysqli_query($conn, $sqlKH));
$khachHang = intval($kh['total']);

/* ===== DỊCH VỤ ===== */
$sqlDV = "
SELECT COUNT(DISTINCT hd_dichvu.maDV) AS total
FROM hoadon
JOIN hd_dichvu ON hoadon.maHD = hd_dichvu.maHD
WHERE YEAR(hoadon.ngayLap) = $nam1
";
$dv = mysqli_fetch_assoc(mysqli_query($conn, $sqlDV));
$dichVu = intval($dv['total']);

echo json_encode(array(
    "nam1" => $nam1,
    "nam2" => $nam2,
    "doanhThuNam1" => $dt1,
    "doanhThuNam2" => $dt2,
    "khachHang" => $khachHang,
    "dichVu" => $dichVu
));
