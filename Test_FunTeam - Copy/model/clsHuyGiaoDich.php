<?php
require_once("clsconnect.php");

class clsHuyGiaoDich {
    private $conn;

    public function __construct() {
        $db = new clsKetNoi();
        $this->conn = $db->moketnoi();
        
        if (!$this->conn) {
            die("Lỗi kết nối database");
        }
    }

    // Tìm kiếm đơn đặt phòng
    public function timKiemGiaoDich($keyword) {
        $sql = "SELECT ddp.maDDP, kh.hoTen, kh.CCCD, ddp.ngayNhanPhong, 
                       ddp.ngayTraPhong, ddp.trangThai, kh.soDienThoai,
                       COUNT(ctdp.maPhong) as soPhong, ddp.idKH
                FROM DonDatPhong ddp
                JOIN KhachHang kh ON ddp.idKH = kh.idKH
                LEFT JOIN ChiTietDatPhong ctdp ON ddp.maDDP = ctdp.maDDP
                WHERE (ddp.maDDP LIKE ? OR kh.CCCD LIKE ? OR kh.hoTen LIKE ? OR kh.soDienThoai LIKE ?) 
                GROUP BY ddp.maDDP
                ORDER BY ddp.ngayTraPhong ASC";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return array();
        }
        
        $search = "%" . $keyword . "%";
        $stmt->bind_param("ssss", $search, $search, $search, $search);
        
        if (!$stmt->execute()) {
            $stmt->close();
            return array();
        }
        
        $stmt->bind_result($maDDP, $hoTen, $CCCD, $ngayNhanPhong, $ngayTraPhong, $trangThai, $soDienThoai, $soPhong, $idKH);
        
        $data = array();
        while ($stmt->fetch()) {
            $data[] = array(
                'maDDP' => $maDDP,
                'hoTen' => $hoTen,
                'CCCD' => $CCCD,
                'ngayNhanPhong' => $ngayNhanPhong,
                'ngayTraPhong' => $ngayTraPhong,
                'trangThai' => $trangThai,
                'soDienThoai' => $soDienThoai,
                'soPhong' => $soPhong,
                'idKH' => $idKH
            );
        }
        
        $stmt->close();
        return $data;
    }

    // Lấy thông tin đầy đủ đơn đặt phòng
    public function layChiTietDonDatPhong($maDDP) {
        error_log("=== BẮT ĐẦU layChiTietDonDatPhong: $maDDP ===");
        
        $result = array(
            'donDatPhong' => null,
            'khachHang' => null,
            'chiTietPhong' => array(),
            'dichVu' => array(),
            'hoaDon' => null,
            'boiThuong' => array(),
            'soNgay' => 0,
            'tongTienPhong' => 0,
            'tongTienDichVu' => 0,
            'tongBoiThuong' => 0,
            'tongTien' => 0
        );
        
        // 1. Lấy thông tin đơn đặt phòng và khách hàng
        $sql = "SELECT ddp.maDDP, ddp.idKH, ddp.maKM, ddp.ngayDatPhong, 
                       ddp.ngayNhanPhong, ddp.ngayTraPhong, ddp.soLuong, 
                       ddp.ghiChu, ddp.trangThai,
                       kh.hoTen, kh.email, kh.soDienThoai, kh.CCCD, 
                       kh.diaChi, kh.loaiKH
                FROM DonDatPhong ddp
                JOIN KhachHang kh ON ddp.idKH = kh.idKH
                WHERE ddp.maDDP = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Lỗi prepare layChiTietDonDatPhong: " . $this->conn->error);
            return $result;
        }
        
        $stmt->bind_param("s", $maDDP);
        
        if (!$stmt->execute()) {
            error_log("Lỗi execute layChiTietDonDatPhong: " . $stmt->error);
            $stmt->close();
            return $result;
        }
        
        $stmt->bind_result(
            $maDDP_db, $idKH, $maKM, $ngayDatPhong, $ngayNhanPhong, $ngayTraPhong, 
            $soLuong, $ghiChu, $trangThai, 
            $hoTen, $email, $soDienThoai, $CCCD, $diaChi, $loaiKH
        );
        
        if ($stmt->fetch()) {
            // Tính số ngày ở
            $soNgay = 1;
            if ($ngayNhanPhong && $ngayTraPhong) {
                $ngayNhanTimestamp = strtotime($ngayNhanPhong);
                $ngayTraTimestamp = strtotime($ngayTraPhong);
                $chenhLechGiay = $ngayTraTimestamp - $ngayNhanTimestamp;
                $soNgay = floor($chenhLechGiay / 86400) + 1;
                if ($soNgay < 1) $soNgay = 1;
            }
            
            $result['donDatPhong'] = array(
                'maDDP' => $maDDP_db,
                'idKH' => $idKH,
                'maKM' => $maKM,
                'ngayDatPhong' => $ngayDatPhong,
                'ngayNhanPhong' => $ngayNhanPhong,
                'ngayTraPhong' => $ngayTraPhong,
                'soNgay' => $soNgay,
                'soLuong' => $soLuong,
                'ghiChu' => $ghiChu,
                'trangThai' => $trangThai
            );
            
            $result['khachHang'] = array(
                'idKH' => $idKH,
                'hoTen' => $hoTen,
                'email' => $email,
                'soDienThoai' => $soDienThoai,
                'CCCD' => $CCCD,
                'diaChi' => $diaChi,
                'loaiKH' => $loaiKH
            );
            
            $result['soNgay'] = $soNgay;
        } else {
            error_log("Không tìm thấy đơn: $maDDP");
            $stmt->close();
            return $result;
        }
        
        $stmt->close();
        
        // 2. Lấy CHÍNH XÁC số lượng phòng từ bảng chi tiết
        $sqlSoLuongPhong = "SELECT COUNT(*) as soLuongPhong 
                           FROM ChiTietDatPhong 
                           WHERE maDDP = ?";
        
        $stmtSL = $this->conn->prepare($sqlSoLuongPhong);
        if ($stmtSL) {
            $stmtSL->bind_param("s", $maDDP);
            $stmtSL->execute();
            $stmtSL->bind_result($soLuongPhongThucTe);
            
            if ($stmtSL->fetch()) {
                if ($result['donDatPhong']) {
                    $result['donDatPhong']['soLuongPhongThucTe'] = $soLuongPhongThucTe;
                }
            }
            $stmtSL->close();
        }
        
        // 3. Lấy chi tiết phòng đã đặt 
        $sqlChiTietPhong = "SELECT ctdp.maPhong, 
                                   lp.tenLoaiPhong, 
                                   p.giaPhong,
                                   p.tangPhong,
                                   p.soPhong,
                                   p.sucChua
                           FROM ChiTietDatPhong ctdp
                           JOIN Phong p ON ctdp.maPhong = p.maPhong
                           JOIN LoaiPhong lp ON p.maLoaiPhong = lp.maLoaiPhong
                           WHERE ctdp.maDDP = ?";
        
        $stmtCTP = $this->conn->prepare($sqlChiTietPhong);
        if ($stmtCTP) {
            $stmtCTP->bind_param("s", $maDDP);
            $stmtCTP->execute();
            $stmtCTP->bind_result($maPhong, $tenLoaiPhong, $giaPhong, $tangPhong, $soPhong, $sucChua);
            
            $tongTienPhong = 0;
            while ($stmtCTP->fetch()) {
                // Tính tiền phòng = giá phòng × số ngày
                $tienPhong = $giaPhong * $result['soNgay'];
                $tongTienPhong += $tienPhong;
                
                $result['chiTietPhong'][] = array(
                    'maPhong' => $maPhong,
                    'tenLoaiPhong' => $tenLoaiPhong,
                    'giaPhong' => $giaPhong,
                    'tienPhong' => $tienPhong,
                    'tangPhong' => $tangPhong,
                    'soPhong' => $soPhong,
                    'sucChua' => $sucChua
                );
            }
            $result['tongTienPhong'] = $tongTienPhong;
            $result['tongTien'] += $tongTienPhong;
            $stmtCTP->close();
        }
        
        // 4. Lấy dịch vụ đã sử dụng (nếu có hóa đơn)
        $sqlDV = "SELECT dv.maDV, dv.tenDV, hddv.soLuong, hddv.donGia, hddv.thanhTien
                  FROM HoaDon hd
                  JOIN hd_dichvu hddv ON hd.maHD = hddv.maHD
                  JOIN DichVu dv ON hddv.maDV = dv.maDV
                  WHERE hd.maDDP = ?";
        
        $stmtDV = $this->conn->prepare($sqlDV);
        if ($stmtDV) {
            $stmtDV->bind_param("s", $maDDP);
            $stmtDV->execute();
            $stmtDV->bind_result($maDV, $tenDV, $soLuongDV, $donGia, $thanhTien);
            
            $tongTienDV = 0;
            while ($stmtDV->fetch()) {
                $result['dichVu'][] = array(
                    'maDV' => $maDV,
                    'tenDV' => $tenDV,
                    'soLuong' => $soLuongDV,
                    'donGia' => $donGia,
                    'thanhTien' => $thanhTien
                );
                $tongTienDV += $thanhTien;
            }
            $result['tongTienDichVu'] = $tongTienDV;
            $result['tongTien'] += $tongTienDV;
            $stmtDV->close();
        }
        
        // 5. Lấy thông tin hóa đơn (nếu có)
        $sqlHD = "SELECT maHD, ngayLap, tongTien, phuongThucThanhToan, ngayThanhToan, trangThai
                  FROM HoaDon
                  WHERE maDDP = ?";
        
        $stmtHD = $this->conn->prepare($sqlHD);
        if ($stmtHD) {
            $stmtHD->bind_param("s", $maDDP);
            $stmtHD->execute();
            $stmtHD->bind_result($maHD, $ngayLap, $tongTienHD, $phuongThucTT, $ngayThanhToan, $trangThaiHD);
            
            if ($stmtHD->fetch()) {
                $result['hoaDon'] = array(
                    'maHD' => $maHD,
                    'ngayLap' => $ngayLap,
                    'tongTien' => $tongTienHD,
                    'phuongThucTT' => $phuongThucTT,
                    'ngayThanhToan' => $ngayThanhToan,
                    'trangThai' => $trangThaiHD
                );
            }
            $stmtHD->close();
        }
        
        // 6. Lấy thông tin bồi thường (nếu có)
        $sqlBT = "SELECT bt.maBT, bt.ngayBT, bt.lyDo, bt.tongBoiThuong
                  FROM BoiThuong bt
                  JOIN Phong p ON bt.maPhong = p.maPhong
                  JOIN ChiTietDatPhong ctdp ON p.maPhong = ctdp.maPhong
                  WHERE ctdp.maDDP = ?";
        
        $stmtBT = $this->conn->prepare($sqlBT);
        if ($stmtBT) {
            $stmtBT->bind_param("s", $maDDP);
            $stmtBT->execute();
            $stmtBT->bind_result($maBT, $ngayBT, $lyDo, $tongBoiThuong);
            
            $tongBT = 0;
            while ($stmtBT->fetch()) {
                $result['boiThuong'][] = array(
                    'maBT' => $maBT,
                    'ngayBT' => $ngayBT,
                    'lyDo' => $lyDo,
                    'tongBoiThuong' => $tongBoiThuong
                );
                $tongBT += $tongBoiThuong;
            }
            $result['tongBoiThuong'] = $tongBT;
            $result['tongTien'] += $tongBT;
            $stmtBT->close();
        }
        
        // 7. Lấy thông tin khuyến mãi nếu có
        if (!empty($result['donDatPhong']['maKM'])) {
            $sqlKM = "SELECT mucGiam FROM KhuyenMai WHERE maKM = ?";
            $stmtKM = $this->conn->prepare($sqlKM);
            if ($stmtKM) {
                $stmtKM->bind_param("s", $result['donDatPhong']['maKM']);
                $stmtKM->execute();
                $stmtKM->bind_result($mucGiam);
                
                if ($stmtKM->fetch()) {
                    $result['khuyenMai'] = array(
                        'maKM' => $result['donDatPhong']['maKM'],
                        'mucGiam' => $mucGiam
                    );
                    
                    // Tính tiền giảm giá
                    if ($mucGiam > 0) {
                        $tienGiamGia = ($result['tongTien'] * $mucGiam) / 100;
                        $result['tienGiamGia'] = $tienGiamGia;
                        $result['tongTien'] -= $tienGiamGia;
                    }
                }
                $stmtKM->close();
            }
        }
        
        error_log("=== KẾT THÚC layChiTietDonDatPhong ===");
        return $result;
    }

    // Kiểm tra có thể hủy đơn không
    public function kiemTraCoTheHuy($maDDP) {
        $sql = "SELECT trangThai, ngayNhanPhong FROM DonDatPhong WHERE maDDP = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Lỗi prepare kiemTraCoTheHuy: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("s", $maDDP);
        
        if (!$stmt->execute()) {
            error_log("Lỗi execute kiemTraCoTheHuy: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $stmt->bind_result($trangThai, $ngayNhanPhong);
        
        if (!$stmt->fetch()) {
            error_log("Không tìm thấy đơn: $maDDP");
            $stmt->close();
            return false;
        }
        
        $stmt->close();
        
        // 1. KIỂM TRA TRẠNG THÁI
        if ($trangThai == 'DaHuy') {
            error_log("Không thể hủy: Đơn $maDDP đã bị hủy trước đó");
            return false;
        }
        
        // 2. KIỂM TRA NGÀY NHẬN PHÒNG
        if (empty($ngayNhanPhong) || $ngayNhanPhong == '0000-00-00') {
            error_log("Không có ngày nhận phòng -> CHO HỦY đơn $maDDP");
            return true;
        }
        
        $ngayHienTai = date('Y-m-d');
        
        if (strtotime($ngayNhanPhong) <= strtotime($ngayHienTai)) {
            error_log("Không thể hủy: Ngày nhận phòng ($ngayNhanPhong) đã qua");
            return false;
        }
        
        error_log("Có thể hủy: Đơn $maDDP chưa hủy, ngày nhận ($ngayNhanPhong) chưa đến");
        return true;
    }
    
    // Hủy đơn đặt phòng
    public function huyDonDatPhong($maDDP) {
        error_log("=== BẮT ĐẦU huyDonDatPhong: $maDDP ===");
        
        // Kiểm tra chi tiết đơn trước
        $chiTietDon = $this->layChiTietDonDatPhong($maDDP);
        
        if (!$chiTietDon || empty($chiTietDon['donDatPhong'])) {
            error_log("Không tìm thấy đơn: $maDDP");
            return false;
        }
        
        $trangThaiHienTai = $chiTietDon['donDatPhong']['trangThai'];
        if ($trangThaiHienTai == 'DaHuy') {
            error_log("Đơn $maDDP đã bị hủy trước đó - KHÔNG THỂ HỦY LẠI");
            return false;
        }
        
        if (!$this->kiemTraCoTheHuy($maDDP)) {
            error_log("Không thể hủy đơn: $maDDP");
            return false;
        }
        
        // Bắt đầu transaction
        $this->conn->autocommit(FALSE);
        $success = false;
        
        try {
            // 1. Cập nhật trạng thái đơn đặt phòng
            $sqlUpdateDDP = "UPDATE DonDatPhong 
                            SET trangThai = 'DaHuy', 
                                ghiChu = CONCAT(IFNULL(ghiChu, ''), ' Hủy ngày: ', CURDATE())
                            WHERE maDDP = ?";
            
            $stmtDDP = $this->conn->prepare($sqlUpdateDDP);
            if (!$stmtDDP) {
                throw new Exception("Lỗi chuẩn bị cập nhật đơn đặt phòng: " . $this->conn->error);
            }
            
            $stmtDDP->bind_param("s", $maDDP);
            if (!$stmtDDP->execute()) {
                throw new Exception("Lỗi thực thi cập nhật đơn đặt phòng: " . $stmtDDP->error);
            }
            $stmtDDP->close();
            
            // 2. Cập nhật trạng thái phòng về trống
            $sqlPhong = "SELECT maPhong FROM ChiTietDatPhong WHERE maDDP = ?";
            $stmtPhong = $this->conn->prepare($sqlPhong);
            if ($stmtPhong) {
                $stmtPhong->bind_param("s", $maDDP);
                $stmtPhong->execute();
                $stmtPhong->bind_result($maPhong);
                
                while ($stmtPhong->fetch()) {
                    $sqlUpdate = "UPDATE Phong SET tinhTrang = 'Trống' WHERE maPhong = ?";
                    $stmtUpdate = $this->conn->prepare($sqlUpdate);
                    if ($stmtUpdate) {
                        $stmtUpdate->bind_param("s", $maPhong);
                        $stmtUpdate->execute();
                        $stmtUpdate->close();
                    }
                }
                $stmtPhong->close();
            }
            
            // 3. Nếu có hóa đơn, cập nhật trạng thái
            $sqlUpdateHD = "UPDATE HoaDon SET trangThai = 'DaHuy' WHERE maDDP = ?";
            $stmtHD = $this->conn->prepare($sqlUpdateHD);
            if ($stmtHD) {
                $stmtHD->bind_param("s", $maDDP);
                $stmtHD->execute();
                $stmtHD->close();
            }
            
            // Commit transaction
            $this->conn->commit();
            $success = true;
            error_log("Hủy đơn thành công: $maDDP");
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Lỗi hủy đơn: " . $e->getMessage());
            $success = false;
        }
        
        $this->conn->autocommit(TRUE);
        error_log("=== KẾT THÚC huyDonDatPhong ===");
        return $success;
    }

    public function __destruct() {
        if($this->conn) {
            $this->conn->close();
        }
    }
}
?>