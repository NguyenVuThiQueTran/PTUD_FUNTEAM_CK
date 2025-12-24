<?php
require_once("clsconnect.php");

class clsLichSuGD {
    private $conn;
    
    public function __construct() {
        $db = new clsKetNoi();
        $this->conn = $db->moketnoi();
    }
    
    // Lấy lịch sử giao dịch đã thanh toán (filtered by customer ID)
    public function layLichSuGiaoDich($tuNgay = null, $denNgay = null, $idKH = null) {
        // If customer ID is provided, filter by that customer
        if ($idKH) {
            $sql = "SELECT h.* 
                    FROM hoadon h
                    INNER JOIN dondatphong d ON h.maDDP = d.maDDP
                    WHERE h.trangThai = 'DaThanhToan' 
                    AND d.idKH = " . intval($idKH);
        } else {
            // Admin view - show all paid transactions
            $sql = "SELECT * FROM hoadon WHERE trangThai = 'DaThanhToan'";
        }
        
        if ($tuNgay) {
            $sql .= " AND ngayThanhToan >= '" . $this->conn->real_escape_string($tuNgay) . "'";
        }
        
        if ($denNgay) {
            $sql .= " AND ngayThanhToan <= '" . $this->conn->real_escape_string($denNgay) . "'";
        }
        
        $sql .= " ORDER BY ngayThanhToan DESC";
        
        $result = $this->conn->query($sql);
        $danhSach = array();
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $danhSach[] = $row;
            }
        }
        return $danhSach;
    }
    
    // Lấy chi tiết hóa đơn đã thanh toán
    public function layChiTietHoaDon($maHD) {
        // Lấy thông tin hóa đơn
        $sql = "SELECT * FROM hoadon WHERE maHD = '" . $this->conn->real_escape_string($maHD) . "'";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $hoaDon = $result->fetch_assoc();
            
            // Lấy thông tin phòng (nếu có)
            if (!empty($hoaDon['maDDP'])) {
                $sqlPhong = "SELECT p.maPhong, p.hangPhong, p.giaPhong 
                            FROM chitietdatphong ct
                            INNER JOIN phong p ON ct.maPhong = p.maPhong
                            WHERE ct.maDDP = '" . $this->conn->real_escape_string($hoaDon['maDDP']) . "'
                            LIMIT 1";
                $resultPhong = $this->conn->query($sqlPhong);
                
                if ($resultPhong && $resultPhong->num_rows > 0) {
                    $hoaDon['phong'] = $resultPhong->fetch_assoc();
                } else {
                    $hoaDon['phong'] = null;
                }
            } else {
                $hoaDon['phong'] = null;
            }
            
            // Lấy dịch vụ bổ sung (từ bảng hd_dichvu)
            $sqlDichVu = "SELECT d.tenDV, d.loaiDV, hd.donGia, hd.soLuong, hd.thanhTien
                         FROM hd_dichvu hd
                         INNER JOIN dichvu d ON hd.maDV = d.maDV
                         WHERE hd.maHD = '" . $this->conn->real_escape_string($maHD) . "'";
            $resultDichVu = $this->conn->query($sqlDichVu);
            
            $dichVu = array();
            if ($resultDichVu && $resultDichVu->num_rows > 0) {
                while($row = $resultDichVu->fetch_assoc()) {
                    $dichVu[] = $row;
                }
            }
            $hoaDon['dichVu'] = $dichVu;
            
            return $hoaDon;
        }
        
        return null;
    }
}
?>