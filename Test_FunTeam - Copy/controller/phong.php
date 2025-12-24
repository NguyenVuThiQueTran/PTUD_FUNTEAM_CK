<?php
// BẬT HIỆN LỖI
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=utf-8");

require_once '../model/clsconnect.php';

try {
    // Tạo đối tượng và kết nối
    $db = new clsKetNoi();
    $conn = $db->moketnoi();
    
    if (!$conn) {
        throw new Exception("Không có kết nối database");
    }
    
    // Lấy tham số
    $loaiPhong = isset($_GET['loaiPhong']) ? $_GET['loaiPhong'] : '';
    $tinhTrang = isset($_GET['tinhTrang']) ? $_GET['tinhTrang'] : '';
    $hangPhong = isset($_GET['hangPhong']) ? $_GET['hangPhong'] : '';
    $soGiuong = isset($_GET['soGiuong']) ? $_GET['soGiuong'] : '';
    $giaMin = isset($_GET['giaMin']) ? (int)$_GET['giaMin'] : 0;
    $giaMax = isset($_GET['giaMax']) ? (int)$_GET['giaMax'] : 50000000;
    
    // Xây dựng SQL
    $sql = "SELECT * FROM phong WHERE 1=1";
    
    if (!empty($loaiPhong)) {
        $sql .= " AND maLoaiPhong = '" . $conn->real_escape_string($loaiPhong) . "'";
    }
    if (!empty($tinhTrang)) {
        $sql .= " AND tinhTrang = '" . $conn->real_escape_string($tinhTrang) . "'";
    }
    if (!empty($hangPhong)) {
        $sql .= " AND hangPhong = '" . $conn->real_escape_string($hangPhong) . "'";
    }
    if (!empty($soGiuong)) {
        $sql .= " AND sucChua = " . (int)$soGiuong;
    }
    
    $sql .= " AND giaPhong BETWEEN $giaMin AND $giaMax";
    
    // Thực thi query - Dùng $conn->query() thay vì mysqli_query()
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // KHÔNG dùng utf8_encode() - dữ liệu đã là UTF-8
            $data[] = $row;
        }
    }
    
    // KHÔNG dùng utf8_encode() ở đây
    echo json_encode($data);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'error' => true,
        'message' => $e->getMessage()
    ));
}

// Đóng kết nối nếu cần
if (isset($conn)) {
    $conn->close();
}
?>