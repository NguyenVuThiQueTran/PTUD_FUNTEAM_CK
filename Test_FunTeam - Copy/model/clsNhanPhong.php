<?php
require_once("clsconnect.php");

class clsNhanPhong {
    private $conn;

    public function __construct() {
        $db = new clsKetNoi();
        $this->conn = $db->moketnoi();
    }

    public function timKiemGiaoDich($keyword) {
    $sql = "SELECT DISTINCT ddp.maDDP, kh.hoTen, kh.CCCD, ddp.ngayNhanPhong, 
                   ddp.trangThai, 
                   (SELECT COUNT(*) FROM ChiTietDatPhong ctdp2 WHERE ctdp2.maDDP = ddp.maDDP) as soLuongPhong,
                   ddp.ngayTraPhong, kh.soDienThoai
            FROM DonDatPhong ddp
            JOIN KhachHang kh ON ddp.idKH = kh.idKH
            WHERE (ddp.maDDP LIKE ? 
                   OR kh.CCCD LIKE ? 
                   OR kh.hoTen LIKE ? 
                   OR kh.soDienThoai LIKE ?) 
              
            ORDER BY ddp.ngayNhanPhong ASC";
    
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        error_log("Lỗi prepare SQL: " . $this->conn->error);
        return array();
    }
    
    $search = "%" . $keyword . "%";
    $stmt->bind_param("ssss", $search, $search, $search, $search);
    
    if (!$stmt->execute()) {
        error_log("Lỗi execute SQL: " . $stmt->error);
        return array();
    }
    
    // Bind kết quả
    $stmt->bind_result($maDDP, $hoTen, $CCCD, $ngayNhanPhong, $trangThai, 
                      $soLuongPhong, $ngayTraPhong, $soDienThoai);
    
    $data = array();
    while ($stmt->fetch()) {
        $data[] = array(
            'maDDP' => $maDDP,
            'hoTen' => $hoTen,
            'CCCD' => $CCCD,
            'ngayNhanPhong' => $ngayNhanPhong,
            'trangThai' => $trangThai,
            'soPhong' => $soLuongPhong, // Số lượng phòng
            'ngayTraPhong' => $ngayTraPhong,
            'soDienThoai' => $soDienThoai
        );
    }
    
    $stmt->close();
    return $data;
}
// Thêm hàm này vào class clsNhanPhong
public function layDanhSachPhong($maDDP) {
    $sql = "SELECT p.soPhong 
            FROM ChiTietDatPhong ctdp
            JOIN Phong p ON ctdp.maPhong = p.maPhong
            WHERE ctdp.maDDP = ?";
    
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        return "";
    }
    
    $stmt->bind_param("s", $maDDP);
    
    if (!$stmt->execute()) {
        $stmt->close();
        return "";
    }
    
    $stmt->bind_result($soLuongPhong);
    
    $danhSachPhong = array();
    while ($stmt->fetch()) {
        $danhSachPhong[] = $soLuongPhong;
    }
    
    $stmt->close();
    
    return implode(", ", $danhSachPhong);
}
    // 2. Lấy chi tiết giao dịch
    public function layChiTietGiaoDich($maDDP) {
    $data = array(
        'donDatPhong' => null,
        'khachHang' => null,
        'chiTietPhong' => array()
    );

    // Lấy thông tin đơn đặt phòng và khách hàng - THÊM CÁC TRƯỜNG THIẾU
    $sql = "SELECT ddp.maDDP, ddp.ngayDatPhong, ddp.ngayNhanPhong, ddp.ngayTraPhong, 
                   ddp.soLuong, ddp.trangThai, ddp.ghiChu,
                   kh.idKH, kh.hoTen, kh.CCCD, kh.email, kh.soDienThoai, 
                   kh.diaChi, kh.loaiKH
            FROM DonDatPhong ddp
            JOIN KhachHang kh ON ddp.idKH = kh.idKH
            WHERE ddp.maDDP = ?";
    
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        error_log("Lỗi prepare SQL chi tiết: " . $this->conn->error);
        return $data;
    }
    
    $stmt->bind_param("s", $maDDP);
    
    if (!$stmt->execute()) {
        error_log("Lỗi execute SQL chi tiết: " . $stmt->error);
        $stmt->close();
        return $data;
    }
    
    // Bind kết quả - ĐÚNG SỐ LƯỢNG CỘT
    $stmt->bind_result(
        $maDDP_db, $ngayDatPhong, $ngayNhanPhong, $ngayTraPhong, 
        $soLuong, $trangThai, $ghiChu,
        $idKH, $hoTen, $CCCD, $email, $soDienThoai, $diaChi, $loaiKH
    );
    
    if ($stmt->fetch()) {
        $data['donDatPhong'] = array(
            'maDDP' => $maDDP_db,
            'ngayDatPhong' => $ngayDatPhong,
            'ngayNhanPhong' => $ngayNhanPhong,
            'ngayTraPhong' => $ngayTraPhong,
            'soLuong' => $soLuong,
            'trangThai' => $trangThai,
            'ghiChu' => $ghiChu
        );
        
        $data['khachHang'] = array(
            'idKH' => $idKH,
            'hoTen' => $hoTen,
            'CCCD' => $CCCD,
            'email' => $email,
            'soDienThoai' => $soDienThoai,
            'diaChi' => $diaChi,
            'loaiKH' => $loaiKH
        );
    }
    
    $stmt->close();

    // Lấy chi tiết phòng đã đặt 
    $sqlPhong = "SELECT p.maPhong, p.soPhong, p.tangPhong, 
                        lp.tenLoaiPhong, p.giaPhong, p.tinhTrang
                 FROM ChiTietDatPhong ctdp
                 JOIN Phong p ON ctdp.maPhong = p.maPhong
                 JOIN LoaiPhong lp ON p.maLoaiPhong = lp.maLoaiPhong
                 WHERE ctdp.maDDP = ?";
    
    $stmt = $this->conn->prepare($sqlPhong);
    if ($stmt) {
        $stmt->bind_param("s", $maDDP);
        if ($stmt->execute()) {
            $stmt->bind_result($maPhong, $soPhong, $tangPhong, $tenLoaiPhong, $giaPhong, $tinhTrang);
            
            while ($stmt->fetch()) {
                $data['chiTietPhong'][] = array(
                    'maPhong' => $maPhong,
                    'soPhong' => $soPhong,
                    'tangPhong' => $tangPhong,
                    'tenLoaiPhong' => $tenLoaiPhong,
                    'giaPhong' => $giaPhong,
                    'tinhTrang' => $tinhTrang
                );
            }
        }
        $stmt->close();
    }

    return $data;
}

    // 3. Kiểm tra thời gian nhận phòng (120 phút)
    public function kiemTraThoiGianNhanPhong($maDDP) {
        $sql = "SELECT ngayNhanPhong 
                FROM DonDatPhong 
                WHERE maDDP = ? AND trangThai = 'DangCho'";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("s", $maDDP);
        
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }
        
        $stmt->bind_result($ngayNhan);
        
        if ($stmt->fetch()) {
            $stmt->close();
            
            // Tạo thời gian check-in: ngày nhận + 14:00:00
            $checkInTime = strtotime($ngayNhan . " 14:00:00");
            $currentTime = time();
            
            // Tính số phút chênh lệch
            $minutesDiff = ($currentTime - $checkInTime) / 60;
            
            // Kiểm tra: nếu currentTime < checkInTime (chưa đến giờ check-in)
            // hoặc trong vòng 120 phút sau check-in
            if ($currentTime < $checkInTime) {
                return true; // Chưa đến giờ check-in, vẫn có thể nhận
            } elseif ($minutesDiff <= 120) {
                return true; // Trong vòng 120 phút sau check-in
            } else {
                return false; // Quá 120 phút sau check-in
            }
        }
        
        $stmt->close();
        return false;
    }

    // 4. Xác nhận nhận phòng
    public function xacNhanNhanPhong($maDDP) {
        $this->conn->autocommit(FALSE); // Tắt autocommit
        $success = false;
        
        try {
            // Cập nhật trạng thái đơn thành "DaNhan"
            $sqlUpdate = "UPDATE DonDatPhong 
                         SET trangThai = 'DaNhan',
                             ghiChu = CONCAT(IFNULL(ghiChu, ''), ' [Nhận phòng: ', NOW(), ']')
                         WHERE maDDP = ? AND trangThai = 'DangCho'";
            
            $stmt = $this->conn->prepare($sqlUpdate);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị câu lệnh SQL: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $maDDP);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi thực thi câu lệnh SQL: " . $stmt->error);
            }
            
            if ($stmt->affected_rows <= 0) {
                throw new Exception("Không thể cập nhật trạng thái. Có thể đơn đã bị thay đổi.");
            }
            $stmt->close();
            
            // Cập nhật trạng thái phòng thành "Đang ở"
            $sqlPhong = "UPDATE Phong p
                        JOIN ChiTietDatPhong ctdp ON p.maPhong = ctdp.maPhong
                        SET p.tinhTrang = 'Đang ở'
                        WHERE ctdp.maDDP = ?";
            
            $stmt = $this->conn->prepare($sqlPhong);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị cập nhật phòng: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $maDDP);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi thực thi cập nhật phòng: " . $stmt->error);
            }
            
            // Commit transaction
            $this->conn->commit();
            $success = true;
            
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->conn->rollback();
            error_log("Lỗi nhận phòng: " . $e->getMessage());
            $success = false;
        }
        
        // Bật lại autocommit
        $this->conn->autocommit(TRUE);
        return $success;
    }

    // 5. Kiểm tra đơn có tồn tại và có thể nhận phòng
    public function kiemTraCoTheNhanPhong($maDDP) {
        $sql = "SELECT COUNT(*) as count 
                FROM DonDatPhong 
                WHERE maDDP = ? AND trangThai = 'DangCho'";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("s", $maDDP);
        
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }
        
        $stmt->bind_result($count);
        
        if ($stmt->fetch()) {
            $stmt->close();
            return $count > 0;
        }
        
        $stmt->close();
        return false;
    }

    // 6. Lấy thông tin phòng để kiểm tra
    public function layTrangThaiPhong($maDDP) {
        $sql = "SELECT p.tinhTrang 
                FROM Phong p
                JOIN ChiTietDatPhong ctdp ON p.maPhong = ctdp.maPhong
                WHERE ctdp.maDDP = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return array();
        }
        
        $stmt->bind_param("s", $maDDP);
        
        if (!$stmt->execute()) {
            $stmt->close();
            return array();
        }
        
        $stmt->bind_result($tinhTrang);
        
        $trangThaiPhong = array();
        while ($stmt->fetch()) {
            $trangThaiPhong[] = $tinhTrang;
        }
        
        $stmt->close();
        return $trangThaiPhong;
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    // Thêm hàm này vào class clsNhanPhong
public function tinhTienPhong($maDDP) {
    // Khởi tạo kết quả
    $result = array(
        'tongTien' => 0,
        'soNgayO' => 1,
        'chiTietTinh' => array(),
        'ngayNhan' => '',
        'ngayTra' => ''
    );
    
    try {
        // 1. Lấy thông tin ngày từ đơn đặt phòng
        $sqlNgay = "SELECT ngayNhanPhong, ngayTraPhong FROM DonDatPhong WHERE maDDP = ?";
        $stmt = $this->conn->prepare($sqlNgay);
        
        if ($stmt) {
            $stmt->bind_param("s", $maDDP);
            $stmt->execute();
            $stmt->bind_result($ngayNhan, $ngayTra);
            
            if ($stmt->fetch()) {
                $result['ngayNhan'] = $ngayNhan;
                $result['ngayTra'] = $ngayTra;
                
                // Tính số ngày ở
                if (!empty($ngayNhan) && !empty($ngayTra)) {
                    $timeNhan = strtotime($ngayNhan);
                    $timeTra = strtotime($ngayTra);
                    
                    if ($timeNhan !== false && $timeTra !== false) {
                        $soNgay = ($timeTra - $timeNhan) / (60 * 60 * 24);
                        $result['soNgayO'] = max(1, ceil($soNgay)); // Ít nhất 1 ngày, làm tròn lên
                    }
                }
            }
            $stmt->close();
        }
        
        // 2. Lấy danh sách phòng và tính tiền
        $sqlPhong = "SELECT p.maPhong, p.soPhong, p.giaPhong, lp.tenLoaiPhong 
                     FROM ChiTietDatPhong ctdp
                     JOIN Phong p ON ctdp.maPhong = p.maPhong
                     LEFT JOIN LoaiPhong lp ON p.maLoaiPhong = lp.maLoaiPhong
                     WHERE ctdp.maDDP = ?";
        
        $stmt = $this->conn->prepare($sqlPhong);
        
        if ($stmt) {
            $stmt->bind_param("s", $maDDP);
            $stmt->execute();
            $stmt->bind_result($maPhong, $soPhong, $giaPhong, $tenLoaiPhong);
            
            $tongTien = 0;
            $chiTiet = array();
            
            while ($stmt->fetch()) {
                $tienPhong = $giaPhong * $result['soNgayO'];
                $tongTien += $tienPhong;
                
                $chiTiet[] = array(
                    'maPhong' => $maPhong,
                    'soPhong' => $soPhong,
                    'giaMotNgay' => $giaPhong,
                    'tenLoaiPhong' => $tenLoaiPhong,
                    'soNgay' => $result['soNgayO'],
                    'tienPhong' => $tienPhong
                );
            }
            
            $result['tongTien'] = $tongTien;
            $result['chiTietTinh'] = $chiTiet;
            $stmt->close();
        }
        
    } catch (Exception $e) {
        error_log("Lỗi tính tiền phòng: " . $e->getMessage());
    }
    
    return $result;
}
}
?>