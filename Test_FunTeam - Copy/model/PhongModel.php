<?php
require_once dirname(__FILE__) . '/../model/clsconnect.php';

class PhongModel extends clsKetNoi {

    public function traCuu($f) {
        // 1. Lấy kết nối bằng cách gọi moketnoi()
        $conn = $this->moketnoi();
        
        // 2. Xây dựng SQL
        $sql = "SELECT * FROM phong WHERE 1=1";

        if (!empty($f['loaiPhong'])) {
            // Dùng mysqli_real_escape_string
            $sql .= " AND maLoaiPhong='" . $conn->real_escape_string($f['loaiPhong']) . "'";
        }
        if (!empty($f['hangPhong'])) {
            $sql .= " AND hangPhong='" . $conn->real_escape_string($f['hangPhong']) . "'";
        }
        if (!empty($f['soGiuong'])) {
            $sql .= " AND sucChua=" . (int)$f['soGiuong'];
        }
        if (!empty($f['giaMin'])) {
            $sql .= " AND giaPhong>=" . (int)$f['giaMin'];
        }
        if (!empty($f['giaMax'])) {
            $sql .= " AND giaPhong<=" . (int)$f['giaMax'];
        }
        if (!empty($f['tinhTrang'])) {
            $sql .= " AND tinhTrang='" . $conn->real_escape_string($f['tinhTrang']) . "'";
        }

        // 3. Thực thi query bằng $conn
        $result = $conn->query($sql);
        
        // 4. Xử lý kết quả
        $data = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        return $data;
    }

    public function capNhatTrangThai($maPhong) {
        $conn = $this->moketnoi();
        $maPhong = $conn->real_escape_string($maPhong);
        $conn->query("UPDATE phong SET tinhTrang='Đã đặt' WHERE maPhong='$maPhong'");
    }
}
?>