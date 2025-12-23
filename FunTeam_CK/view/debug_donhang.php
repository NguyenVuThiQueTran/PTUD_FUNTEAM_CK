<?php
session_start();
require_once("../model/clsconnect.php");

$db = new clsKetNoi();
$conn = $db->moketnoi();

echo "<h1>DEBUG ĐƠN HÀNG</h1>";

// Test với mã DDP002
$maDDP = "DDP002";

// 1. Kiểm tra kết nối
echo "<h2>1. Kiểm tra kết nối:</h2>";
if (!$conn) {
    die("✗ Lỗi kết nối database");
}
echo "✓ Kết nối database thành công<br>";

// 2. Kiểm tra bảng DonDatPhong
echo "<h2>2. Kiểm tra bảng DonDatPhong:</h2>";
$sql_check_table = "SHOW TABLES LIKE 'DonDatPhong'";
$result_table = $conn->query($sql_check_table);
if ($result_table && $result_table->num_rows > 0) {
    echo "✓ Bảng DonDatPhong tồn tại<br>";
} else {
    echo "✗ Bảng DonDatPhong KHÔNG tồn tại<br>";
    die();
}

// 3. Kiểm tra đơn DDP002
echo "<h2>3. Kiểm tra đơn DDP002:</h2>";
$sql = "SELECT * FROM DonDatPhong WHERE maDDP = '$maDDP'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Tìm thấy đơn DDP002<br>";
    echo "<pre>";
    print_r($row);
    echo "</pre>";
    
    // 4. Kiểm tra JOIN với KhachHang
    echo "<h2>4. Kiểm tra JOIN với KhachHang:</h2>";
    $idKH = $row['idKH'];
    $sql_join = "SELECT ddp.*, kh.hoTen, kh.email 
                 FROM DonDatPhong ddp 
                 JOIN KhachHang kh ON ddp.idKH = kh.idKH 
                 WHERE ddp.maDDP = '$maDDP'";
    
    $result_join = $conn->query($sql_join);
    if ($result_join && $result_join->num_rows > 0) {
        $row_join = $result_join->fetch_assoc();
        echo "✓ JOIN thành công<br>";
        echo "<pre>";
        print_r($row_join);
        echo "</pre>";
    } else {
        echo "✗ JOIN thất bại<br>";
        echo "Lỗi: " . $conn->error . "<br>";
        
        // Kiểm tra khách hàng riêng
        echo "<h3>Kiểm tra khách hàng idKH = $idKH:</h3>";
        $sql_kh = "SELECT * FROM KhachHang WHERE idKH = $idKH";
        $result_kh = $conn->query($sql_kh);
        if ($result_kh && $result_kh->num_rows > 0) {
            echo "✓ Tìm thấy khách hàng<br>";
            print_r($result_kh->fetch_assoc());
        } else {
            echo "✗ Không tìm thấy khách hàng idKH = $idKH<br>";
        }
    }
    
} else {
    echo "✗ KHÔNG tìm thấy đơn DDP002<br>";
    
    // Hiển thị tất cả các đơn để xem mã nào tồn tại
    echo "<h3>Danh sách các đơn hiện có:</h3>";
    $sql_all = "SELECT maDDP, idKH, ngayDatPhong, trangThai FROM DonDatPhong LIMIT 10";
    $result_all = $conn->query($sql_all);
    if ($result_all && $result_all->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>maDDP</th><th>idKH</th><th>ngayDatPhong</th><th>trangThai</th></tr>";
        while ($row_all = $result_all->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row_all['maDDP'] . "</td>";
            echo "<td>" . $row_all['idKH'] . "</td>";
            echo "<td>" . $row_all['ngayDatPhong'] . "</td>";
            echo "<td>" . $row_all['trangThai'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

$conn->close();
?>