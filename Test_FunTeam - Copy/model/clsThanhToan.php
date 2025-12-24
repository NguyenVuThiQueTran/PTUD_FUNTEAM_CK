<?php
require_once("clsconnect.php");

class clsThanhToan {
    private $conn;
    
    public function __construct() {
        $db = new clsKetNoi();
        $this->conn = $db->moketnoi();
    }
    
    // Lấy danh sách hóa đơn chưa thanh toán (filtered by customer ID)
    public function layDanhSachHoaDonChuaThanhToan($idKH = null) {
        // If customer ID is provided, filter by that customer
        if ($idKH) {
            $sql = "SELECT h.* 
                    FROM hoadon h
                    INNER JOIN dondatphong d ON h.maDDP = d.maDDP
                    WHERE h.trangThai = 'ChuaThanhToan' 
                    AND d.idKH = " . intval($idKH) . "
                    ORDER BY h.ngayLap DESC";
        } else {
            // Admin view - show all unpaid invoices
            $sql = "SELECT * FROM hoadon WHERE trangThai = 'ChuaThanhToan' ORDER BY ngayLap DESC";
        }
        
        $result = $this->conn->query($sql);
        
        $danhSach = array();
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $danhSach[] = $row;
            }
        }
        return $danhSach;
    }
    
    // Lấy chi tiết hóa đơn
    public function layChiTietHoaDon($maHD) {
        // Get invoice info
        $sql = "SELECT h.*, d.idKH, k.hoTen as tenKH 
                FROM hoadon h
                LEFT JOIN dondatphong d ON h.maDDP = d.maDDP
                LEFT JOIN khachhang k ON d.idKH = k.idKH
                WHERE h.maHD = '" . $this->conn->real_escape_string($maHD) . "'";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $hoaDon = $result->fetch_assoc();
            
            // Get service details
            $sqlDichVu = "SELECT hd.*, dv.tenDV, dv.donGia
                          FROM hd_dichvu hd
                          INNER JOIN dichvu dv ON hd.maDV = dv.maDV
                          WHERE hd.maHD = '" . $this->conn->real_escape_string($maHD) . "'";
            $resultDV = $this->conn->query($sqlDichVu);
            
            $hoaDon['dichVu'] = array();
            if ($resultDV && $resultDV->num_rows > 0) {
                while($row = $resultDV->fetch_assoc()) {
                    $hoaDon['dichVu'][] = $row;
                }
            }
            
            return $hoaDon;
        }
        return null;
    }
    
    // Thanh toán tiền mặt
    public function thanhToanTienMat($maHD) {
        $sql = "UPDATE hoadon 
                SET trangThai = 'ChoThanhToan',
                    phuongThucThanhToan = 'Tiền mặt',
                    ngayThanhToan = '" . date('Y-m-d') . "'
                WHERE maHD = '" . $this->conn->real_escape_string($maHD) . "'";
        
        if ($this->conn->query($sql)) {
            return array('success' => true, 'message' => 'Đã gửi yêu cầu thanh toán tiền mặt!');
        }
        return array('success' => false, 'message' => 'Có lỗi xảy ra!');
    }
    
    // Thanh toán chuyển khoản
    public function thanhToanChuyenKhoan($maHD, $maGiaoDich) {
        $sql = "UPDATE hoadon 
                SET trangThai = 'DaThanhToan',
                    phuongThucThanhToan = 'Chuyển khoản',
                    ngayThanhToan = '" . date('Y-m-d') . "',
                    noiDungChuyenKhoan = '" . $this->conn->real_escape_string($maGiaoDich) . "'
                WHERE maHD = '" . $this->conn->real_escape_string($maHD) . "'";
        
        if ($this->conn->query($sql)) {
            return array('success' => true, 'message' => 'Thanh toán thành công!');
        }
        return array('success' => false, 'message' => 'Có lỗi xảy ra!');
    }
    
    // Tạo mã giao dịch ngẫu nhiên
    public function taoMaGiaoDich() {
        return 'GD' . date('YmdHis') . rand(1000, 9999);
    }
}
?>