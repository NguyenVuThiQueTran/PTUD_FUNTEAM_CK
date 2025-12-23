<?php
require_once("clsconnect.php");

class clsDichVu extends clsKetNoi {
    public $conn;
    
    public function __construct() {
        $db = new clsKetNoi();
        $this->conn = $db->moketnoi();
    }
    
    // Lấy danh sách loại dịch vụ
    public function layDanhSachLoai() {
        $sql = "SELECT DISTINCT loaiDV FROM dichvu ORDER BY loaiDV";
        $result = $this->conn->query($sql);
        $danhSach = array();
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $danhSach[] = $row['loaiDV'];
            }
        }
        
        return $danhSach;
    }
    
    // Lấy danh sách dịch vụ với filter
    public function layDanhSachDichVu($loaiDV = null, $giaMin = null, $giaMax = null) {
        $sql = "SELECT 
                    d.maDV,
                    d.tenDV,
                    d.loaiDV,
                    d.donGia,
                    d.rating,
                    d.trangThai,
                    d.moTa,
                    d.soLuongConLai as soLuongKhaDung
                FROM dichvu d
                WHERE 1=1";
        
        if ($loaiDV) {
            $sql .= " AND d.loaiDV = '" . $this->conn->real_escape_string($loaiDV) . "'";
        }
        
        if ($giaMin !== null) {
            $sql .= " AND d.donGia >= " . floatval($giaMin);
        }
        
        if ($giaMax !== null) {
            $sql .= " AND d.donGia <= " . floatval($giaMax);
        }
        
        $sql .= " ORDER BY 
                    (d.soLuongConLai > 0) DESC,
                    d.rating DESC,
                    d.donGia DESC";
        
        $result = $this->conn->query($sql);
        $danhSach = array();
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $row['trangThaiThucTe'] = ($row['soLuongKhaDung'] > 0) ? 'Còn' : 'Hết';
                $row['avgRating'] = $row['rating'];
                $danhSach[] = $row;
            }
        }
        
        return $danhSach;
    }
    
    // Lấy chi tiết dịch vụ
    public function layChiTietDichVu($maDV) {
        $sql = "SELECT 
                    d.*,
                    d.soLuongConLai as soLuongKhaDung
                FROM dichvu d
                WHERE d.maDV = '" . $this->conn->real_escape_string($maDV) . "'";
        
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $row['trangThaiThucTe'] = ($row['soLuongKhaDung'] > 0) ? 'Available' : 'Sold Out';
            return $row;
        }
        
        return null;
    }
    
    // Lấy thông tin khách hàng
    public function layThongTinKhachHang($idKH) {
        $sql = "SELECT * FROM khachhang WHERE idKH = " . intval($idKH);
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    // Tạo mã hóa đơn mới
    private function taoMaHoaDonMoi() {
        $sql = "SELECT maHD FROM hoadon ORDER BY maHD DESC LIMIT 1";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $maHDCu = $row['maHD'];
            
            // Kiểm tra nếu mã HD đang ở dạng HD2516 (HD + số)
            if (preg_match('/HD(\d+)/', $maHDCu, $matches)) {
                $soThuTu = intval($matches[1]) + 1;
                return 'HD' . $soThuTu;
            } else {
                // Fallback: sử dụng timestamp
                return 'HD' . time();
            }
        }
        
        return 'HD' . time();
    }
    
    // Tạo mã đặt phòng mới cho dịch vụ
    private function taoMaDatPhongMoi($idKH) {
        return 'DDP_DV_' . time() . '_' . $idKH;
    }
    
    // Xác nhận đặt dịch vụ - CẬP NHẬT TỔNG TIỀN VÀO HÓA ĐƠN
    public function xacNhanDatDichVu($idKH, $maDV, $soLuong) {
        // Bắt đầu transaction
        $this->conn->autocommit(FALSE);
        $success = false;
        $message = '';
        $maHD = '';
        $tongTien = 0;
        
        try {
            // 1. Kiểm tra dịch vụ
            $dichVu = $this->layChiTietDichVu($maDV);
            if (!$dichVu) {
                throw new Exception('Không tìm thấy dịch vụ!');
            }
            
            if ($dichVu['soLuongKhaDung'] < $soLuong) {
                throw new Exception('Không đủ số lượng! Chỉ còn ' . $dichVu['soLuongKhaDung'] . ' sản phẩm');
            }
            
            // 2. Tính tổng tiền
            $tongTien = $dichVu['donGia'] * $soLuong;
            
            // 3. Trừ số lượng từ inventory
            $sql = "UPDATE dichvu 
                    SET soLuongConLai = soLuongConLai - " . intval($soLuong) . " 
                    WHERE maDV = '" . $this->conn->real_escape_string($maDV) . "' 
                    AND soLuongConLai >= " . intval($soLuong);
            
            if (!$this->conn->query($sql)) {
                throw new Exception('Lỗi cập nhật kho: ' . $this->conn->error);
            }
            
            if ($this->conn->affected_rows == 0) {
                throw new Exception('Không thể cập nhật kho! Dịch vụ có thể đã hết.');
            }
            
            // 4. Tạo mã đơn đặt phòng và hóa đơn
            $maDDP = $this->taoMaDatPhongMoi($idKH);
            $maHD = $this->taoMaHoaDonMoi();
            $ngayHienTai = date('Y-m-d');
            
            // 5. Tạo đơn đặt phòng
            $sql = "INSERT INTO dondatphong (maDDP, idKH, ngayDatPhong, ngayNhanPhong, ngayTraPhong, trangThai)
                    VALUES (
                        '" . $this->conn->real_escape_string($maDDP) . "',
                        " . intval($idKH) . ",
                        '" . $ngayHienTai . "',
                        '" . $ngayHienTai . "',
                        '" . $ngayHienTai ."',
                        'DaDat'
                    )";
            
            if (!$this->conn->query($sql)) {
                throw new Exception('Lỗi tạo đơn đặt phòng: ' . $this->conn->error);
            }
            
            // 6. Tạo hóa đơn VỚI TỔNG TIỀN
            $sql = "INSERT INTO hoadon 
                    (maHD, maDDP, idKH, ngayLap, tienDichVu, tongTien, trangThai) 
                    VALUES (
                        '" . $this->conn->real_escape_string($maHD) . "',
                        '" . $this->conn->real_escape_string($maDDP) . "',
                        " . intval($idKH) . ",
                        '" . $ngayHienTai . "',
                        " . floatval($tongTien) . ",
                        " . floatval($tongTien) . ",
                        'ChuaThanhToan'
                    )";
            
            if (!$this->conn->query($sql)) {
                throw new Exception('Lỗi tạo hóa đơn: ' . $this->conn->error);
            }
            
            // 7. Thêm chi tiết dịch vụ vào hóa đơn
            $thanhTien = $tongTien;
            $sql = "INSERT INTO hd_dichvu (maHD, maDV, soLuong, donGia, thanhTien)
                    VALUES (
                        '" . $this->conn->real_escape_string($maHD) . "',
                        '" . $this->conn->real_escape_string($maDV) . "',
                        " . intval($soLuong) . ",
                        " . floatval($dichVu['donGia']) . ",
                        " . floatval($thanhTien) . "
                    )";
            
            if (!$this->conn->query($sql)) {
                throw new Exception('Lỗi thêm chi tiết dịch vụ: ' . $this->conn->error);
            }
            
            // Commit transaction
            $this->conn->commit();
            $success = true;
            $message = 'Đặt dịch vụ thành công! Mã hóa đơn: ' . $maHD;
            
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->conn->rollback();
            $success = false;
            $message = $e->getMessage();
        }
        
        $this->conn->autocommit(TRUE);
        
        return array(
            'success' => $success, 
            'message' => $message,
            'maHD' => $maHD,
            'tongTien' => $tongTien
        );
    }
    
    // Hàm kiểm tra và cập nhật tổng tiền cho hóa đơn
    public function capNhatTongTienHoaDon($maHD) {
        // Tính tổng tiền từ chi tiết dịch vụ
        $sql = "SELECT SUM(thanhTien) as tongDichVu 
                FROM hd_dichvu 
                WHERE maHD = '" . $this->conn->real_escape_string($maHD) . "'";
        
        $result = $this->conn->query($sql);
        $tongDichVu = 0;
        
        if ($result && $row = $result->fetch_assoc()) {
            $tongDichVu = floatval($row['tongDichVu']);
        }
        
        // Cập nhật tổng tiền vào hóa đơn
        $sql = "UPDATE hoadon 
                SET tienDichVu = " . $tongDichVu . ",
                    tongTien = tienPhong + " . $tongDichVu . " + tienBoiThuong - giamGia
                WHERE maHD = '" . $this->conn->real_escape_string($maHD) . "'";
        
        return $this->conn->query($sql);
    }
}
?>