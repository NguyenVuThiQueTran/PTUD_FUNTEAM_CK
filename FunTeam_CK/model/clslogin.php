<?php
include_once("clsconnect.php");
class nodeUser{

    private function getPDO() {
        $connObj = new clsKetNoi();
        $conn = $connObj->moketnoi();
        return $conn;
    }
    
    public function mLogin($email, $Pwd) {
        $connObj = new clsKetNoi();
        $conn = $connObj->moketnoi();
        
        $sql = "SELECT 
                    tk.idUser, tk.email, tk.matKhau, tk.loaiTaiKhoan, tk.vaiTro,
                    tk.idThamChieu, tk.trangThai,
                    kh.hoTen, kh.soDienThoai, kh.loaiKH, kh.idKH,
                    d.tenDoan, d.maDoan, d.soLuong,
                    ns.maNS, ns.hoTen as nsHoTen, ns.soDienThoai as nsSoDienThoai,
                    lt.ngoaiNgu, lt.kyNang,
                    nvbp.khuVucPhuTrach, nvbp.phuCap
                FROM taikhoan tk
                LEFT JOIN khachhang kh ON tk.loaiTaiKhoan = 'KhachHang' AND tk.idThamChieu = kh.idKH
                LEFT JOIN doan d ON tk.loaiTaiKhoan = 'Doan' AND tk.idThamChieu = d.maDoan
                LEFT JOIN nhansu ns ON tk.loaiTaiKhoan = 'NhanVien' AND tk.idUser = ns.idUser
                LEFT JOIN letan lt ON ns.maNS = lt.maNS
                LEFT JOIN nhanvienbuongphong nvbp ON ns.maNS = nvbp.maNS
                WHERE tk.email = '$email' AND tk.matKhau = '$Pwd' AND tk.trangThai = 'HoatDong'";
        
        $result = $conn->query($sql);
        $connObj->dongketnoi();
        return $result;
    }
    
    public function mGetUserRoleInfo($email, $Pwd) {
        $connObj = new clsKetNoi();
        $conn = $connObj->moketnoi();
        
        $sql = "SELECT 
                    tk.idUser, 
                    tk.email, 
                    tk.loaiTaiKhoan, 
                    tk.vaiTro,
                    tk.idThamChieu,
                    
                    kh.idKH, 
                    kh.hoTen as khHoTen, 
                    kh.loaiKH,
                    
                    d.maDoan, 
                    d.tenDoan,
                    
                    ns.maNS, 
                    ns.hoTen as nsHoTen, 
                    ns.gioiTinh,
                    
                    CASE 
                        WHEN tk.loaiTaiKhoan = 'Admin' THEN 'admin'
                        WHEN tk.loaiTaiKhoan = 'NhanVien' THEN 
                            CASE 
                                WHEN tk.vaiTro = 'quanly' THEN 'quanly'
                                WHEN tk.vaiTro = 'ketoan' THEN 'ketoan'
                                WHEN tk.vaiTro = 'letan' THEN 'letan'
                                WHEN tk.vaiTro = 'buongphong' THEN 'buongphong'
                                WHEN lt.maNS IS NOT NULL THEN 'letan'
                                WHEN nvbp.maNS IS NOT NULL THEN 'buongphong'
                                ELSE 'nhanvien'
                            END
                        WHEN tk.loaiTaiKhoan = 'Doan' THEN 'doan'
                        WHEN tk.loaiTaiKhoan = 'KhachHang' THEN 'khachhang'
                        ELSE 'khachhang'
                    END as user_role
                    
                FROM taikhoan tk
                LEFT JOIN khachhang kh ON tk.loaiTaiKhoan = 'KhachHang' AND tk.idThamChieu = kh.idKH
                LEFT JOIN doan d ON tk.loaiTaiKhoan = 'Doan' AND tk.idThamChieu = d.maDoan
                LEFT JOIN nhansu ns ON tk.loaiTaiKhoan = 'NhanVien' AND tk.idUser = ns.idUser
                LEFT JOIN letan lt ON ns.maNS = lt.maNS
                LEFT JOIN nhanvienbuongphong nvbp ON ns.maNS = nvbp.maNS
                WHERE tk.email = '$email' 
                AND tk.matKhau = '$Pwd' 
                AND tk.trangThai = 'HoatDong'";
        
        $result = $conn->query($sql);
        $connObj->dongketnoi();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    public function mGetAllCustomers() {
        $conn = $this->getPDO();
        $sql = "SELECT idKH, hoTen, email FROM khachhang WHERE trangThai = 'HoatDong' ORDER BY hoTen";
        $result = $conn->query($sql);
        $customers = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
        }
        return $customers;
    }

    public function mGetAvailableRooms() {
        $conn = $this->getPDO();
        $sql = "SELECT p.maPhong, p.soPhong, p.tangPhong, lp.tenLoaiPhong 
                FROM phong p 
                JOIN loaiphong lp ON p.maLoaiPhong = lp.maLoaiPhong 
                WHERE p.tinhTrang = 'Trống' 
                ORDER BY p.tangPhong, p.soPhong";
        $result = $conn->query($sql);
        $rooms = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
        }
        return $rooms;
    }

    public function mGetStayingGuests() {
        $conn = $this->getPDO();
        $today = date('Y-m-d');
        $sql = "SELECT dp.maDDP, kh.hoTen, p.soPhong 
                FROM dondatphong dp
                JOIN khachhang kh ON dp.idKH = kh.idKH
                JOIN chitietdatphong cdp ON dp.maDDP = cdp.maDDP
                JOIN phong p ON cdp.maPhong = p.maPhong
                WHERE dp.trangThai = 'DaNhan' 
                AND dp.ngayTraPhong >= '$today'
                ORDER BY dp.ngayNhanPhong";
        $result = $conn->query($sql);
        $guests = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $guests[] = $row;
            }
        }
        return $guests;
    }

    public function mGetRecentBookings() {
        $conn = $this->getPDO();
        $sql = "SELECT dp.*, kh.hoTen, p.soPhong, lp.tenLoaiPhong 
                FROM dondatphong dp
                JOIN khachhang kh ON dp.idKH = kh.idKH
                JOIN chitietdatphong cdp ON dp.maDDP = cdp.maDDP
                JOIN phong p ON cdp.maPhong = p.maPhong
                JOIN loaiphong lp ON p.maLoaiPhong = lp.maLoaiPhong
                WHERE dp.ngayDatPhong >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY dp.ngayDatPhong DESC
                LIMIT 10";
        $result = $conn->query($sql);
        $bookings = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
        }
        return $bookings;
    }

    public function mGetTodayCheckins() {
        $conn = $this->getPDO();
        $today = date('Y-m-d');
        $sql = "SELECT dp.*, kh.hoTen, p.soPhong, lp.tenLoaiPhong 
                FROM dondatphong dp
                JOIN khachhang kh ON dp.idKH = kh.idKH
                JOIN chitietdatphong cdp ON dp.maDDP = cdp.maDDP
                JOIN phong p ON cdp.maPhong = p.maPhong
                JOIN loaiphong lp ON p.maLoaiPhong = lp.maLoaiPhong
                WHERE dp.ngayNhanPhong = '$today' 
                AND dp.trangThai = 'DaNhan'
                ORDER BY dp.ngayNhanPhong";
        $result = $conn->query($sql);
        $checkins = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $checkins[] = $row;
            }
        }
        return $checkins;
    }

    public function mGetTodayCheckouts() {
        $conn = $this->getPDO();
        $today = date('Y-m-d');
        $sql = "SELECT dp.*, kh.hoTen, p.soPhong, lp.tenLoaiPhong 
                FROM dondatphong dp
                JOIN khachhang kh ON dp.idKH = kh.idKH
                JOIN chitietdatphong cdp ON dp.maDDP = cdp.maDDP
                JOIN phong p ON cdp.maPhong = p.maPhong
                JOIN loaiphong lp ON p.maLoaiPhong = lp.maLoaiPhong
                WHERE dp.ngayTraPhong = '$today' 
                AND dp.trangThai = 'DaTra'
                ORDER BY dp.ngayTraPhong";
        $result = $conn->query($sql);
        $checkouts = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $checkouts[] = $row;
            }
        }
        return $checkouts;
    }
    
    public function mGetRoomStatistics() {
        $conn = $this->getPDO();
        
        $sql = "SELECT tinhTrang, COUNT(*) as count FROM phong GROUP BY tinhTrang";
        $result = $conn->query($sql);
        $roomStatus = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $roomStatus[$row['tinhTrang']] = $row['count'];
            }
        }
        
        $sql = "SELECT lp.tenLoaiPhong, COUNT(p.maPhong) as count 
                FROM phong p 
                JOIN loaiphong lp ON p.maLoaiPhong = lp.maLoaiPhong 
                GROUP BY p.maLoaiPhong";
        $result = $conn->query($sql);
        $roomTypes = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $roomTypes[$row['tenLoaiPhong']] = $row['count'];
            }
        }
        
        return array(
            'room_status' => $roomStatus,
            'room_types' => $roomTypes
        );
    }
    
    public function mRegis($email, $Pwd) {
        $connObj = new clsKetNoi();
        $conn = $connObj->moketnoi();
        
        $hoTen = $this->extractNameFromEmail($email);
        $currentDate = date('Y-m-d');
        
        $sql1 = "INSERT INTO khachhang (hoTen, email, matKhau, loaiKH, vaiTro, trangThai) 
                VALUES ('$hoTen', '$email', '$Pwd', 'Thuong', 'KhachHang', 'HoatDong')";
        
        if ($conn->query($sql1)) {
            $idKH = $conn->insert_id;
            
            $sql2 = "INSERT INTO taikhoan (email, matKhau, loaiTaiKhoan, idThamChieu, vaiTro, trangThai, ngayTao) 
                    VALUES ('$email', '$Pwd', 'KhachHang', $idKH, 'KhachHang', 'HoatDong', '$currentDate')";
            
            $kq = $conn->query($sql2);
            $connObj->dongketnoi();
            return $kq;
        }
        
        $connObj->dongketnoi();
        return false;
    }

    public function mCheckEmail($email) {
        $connObj = new clsKetNoi();
        $conn = $connObj->moketnoi();
        $sql = "SELECT email FROM taikhoan WHERE email = '$email'";
        $result = $conn->query($sql);
        $connObj->dongketnoi();
        return $result;
    }
    
    public function mGetDashboardData($role, $userId = null) {
        $connObj = new clsKetNoi();
        $conn = $connObj->moketnoi();
        
        $data = array();
        
        switch($role) {
            case 'admin':
                $data['tong_phong'] = $this->getTotalRooms($conn);
                $data['tong_khach'] = $this->getTotalCustomers($conn);
                $data['tong_nhanvien'] = $this->getTotalEmployees($conn);
                $data['doanh_thu'] = $this->getRevenue($conn);
                break;
                
            case 'quanly':
                $data['phong_trong'] = $this->getAvailableRooms($conn);
                $data['phong_dat'] = $this->getBookedRooms($conn);
                $data['khach_luu_tru'] = $this->getStayingGuests($conn);
                $data['ty_le_lap_day'] = $this->getOccupancyRate($conn);
                break;
                
            case 'ketoan':
                $data['doanh_thu_ngay'] = $this->getDailyRevenue($conn);
                $data['doanh_thu_thang'] = $this->getMonthlyRevenue($conn);
                $data['hoa_don_cho'] = $this->getPendingInvoices($conn);
                $data['no_phai_thu'] = $this->getReceivables($conn);
                break;
                
            case 'letan':
                $data['checkin_cho'] = $this->getPendingCheckins($conn);
                $data['checkout_hom_nay'] = $this->getTodayCheckouts($conn);
                $data['yeu_cau_dich_vu'] = $this->getServiceRequests($conn);
                $data['danh_gia_moi'] = $this->getNewReviews($conn);
                break;
                
            case 'buongphong':
                $data['phong_can_don'] = $this->getRoomsToClean($conn);
                $data['phong_sach'] = $this->getCleanRooms($conn);
                $data['phong_bao_tri'] = $this->getMaintenanceRooms($conn);
                $data['cong_viec_hoan_thanh'] = $this->getCompletedTasks($conn);
                break;
                
            default:
                if ($userId) {
                    $data['don_dat_hien_tai'] = $this->getCurrentBookings($conn, $userId);
                    $data['don_da_huy'] = $this->getCancelledBookings($conn, $userId);
                    $data['lich_su_dat'] = $this->getBookingHistory($conn, $userId);
                }
                break;
        }
        
        $connObj->dongketnoi();
        return $data;
    }
    
    private function getTotalRooms($conn) {
        $sql = "SELECT COUNT(*) as total FROM phong";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getTotalCustomers($conn) {
        $sql = "SELECT COUNT(*) as total FROM khachhang WHERE trangThai = 'HoatDong'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getTotalEmployees($conn) {
        $sql = "SELECT COUNT(DISTINCT tk.idUser) as total 
                FROM taikhoan tk 
                LEFT JOIN nhansu ns ON tk.idUser = ns.idUser 
                WHERE tk.loaiTaiKhoan = 'NhanVien' AND tk.trangThai = 'HoatDong'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getRevenue($conn) {
        $sql = "SELECT SUM(tongTien) as total FROM hoadon WHERE trangThai = 'DaThanhToan'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getAvailableRooms($conn) {
        $sql = "SELECT COUNT(*) as total FROM phong WHERE tinhTrang = 'Trong' OR tinhTrang = 'Trống'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getBookedRooms($conn) {
        $sql = "SELECT COUNT(*) as total FROM phong WHERE tinhTrang = 'DaDat'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getStayingGuests($conn) {
        $sql = "SELECT COUNT(*) as total FROM dondatphong 
                WHERE trangThai = 'DaNhan'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getOccupancyRate($conn) {
        $sql = "SELECT 
                    (SUM(CASE WHEN tinhTrang = 'DaDat' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as occupancy_rate 
                FROM phong";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['occupancy_rate']) ? $row['occupancy_rate'] : 0;
        }
        return 0;
    }

    private function getPendingCheckins($conn) {
        $sql = "SELECT COUNT(*) as total FROM dondatphong 
                WHERE trangThai = 'DangCho' AND ngayNhanPhong = CURDATE()";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getRoomsToClean($conn) {
        $sql = "SELECT COUNT(*) as total FROM phong 
                WHERE tinhTrang = 'Bẩn' OR tinhTrang = 'CanDon'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getCleanRooms($conn) {
        $sql = "SELECT COUNT(*) as total FROM phong 
                WHERE tinhTrang = 'Sach' OR tinhTrang = 'Sạch'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getMaintenanceRooms($conn) {
        $sql = "SELECT COUNT(*) as total FROM phong 
                WHERE tinhTrang = 'BaoTri' OR tinhTrang = 'Bảo Trì'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getDailyRevenue($conn) {
        $sql = "SELECT SUM(tongTien) as total FROM hoadon 
                WHERE trangThai = 'DaThanhToan' AND DATE(ngayThanhToan) = CURDATE()";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getReceivables($conn) {
        $sql = "SELECT SUM(conLai) as total FROM hoadon WHERE trangThai = 'ChoThanhToan'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getMonthlyRevenue($conn) {
        $sql = "SELECT SUM(tongTien) as total FROM hoadon 
                WHERE trangThai = 'DaThanhToan' AND MONTH(ngayThanhToan) = MONTH(CURDATE()) 
                AND YEAR(ngayThanhToan) = YEAR(CURDATE())";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getTodayCheckouts($conn) {
        $sql = "SELECT COUNT(*) as total FROM dondatphong 
                WHERE trangThai = 'DaNhan' AND ngayTraPhong = CURDATE()";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getServiceRequests($conn) {
        $sql = "SELECT COUNT(*) as total FROM dichvuyc WHERE trangThai = 'DangXuLy'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getPendingInvoices($conn) {
        $sql = "SELECT COUNT(*) as total FROM hoadon 
                WHERE trangThai = 'ChoThanhToan'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getNewReviews($conn) {
        $sql = "SELECT COUNT(*) as total FROM danhgia WHERE trangThai = 'Moi'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getCompletedTasks($conn) {
        $sql = "SELECT COUNT(*) as total FROM congviec WHERE trangThai = 'HoanThanh'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getCurrentBookings($conn, $userId) {
        $sql = "SELECT COUNT(*) as total FROM dondatphong ddp
                LEFT JOIN khachhang kh ON ddp.idKH = kh.idKH
                LEFT JOIN taikhoan tk ON kh.email = tk.email
                WHERE tk.idUser = $userId 
                AND ddp.trangThai IN ('DangCho', 'DaNhan')";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getCancelledBookings($conn, $userId) {
        $sql = "SELECT COUNT(*) as total FROM dondatphong ddp
                LEFT JOIN khachhang kh ON ddp.idKH = kh.idKH
                LEFT JOIN taikhoan tk ON kh.email = tk.email
                WHERE tk.idUser = $userId 
                AND ddp.trangThai = 'DaHuy'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? $row['total'] : 0;
        }
        return 0;
    }

    private function getBookingHistory($conn, $userId) {
        $sql = "SELECT * FROM dondatphong ddp
                LEFT JOIN khachhang kh ON ddp.idKH = kh.idKH
                LEFT JOIN taikhoan tk ON kh.email = tk.email
                WHERE tk.idUser = $userId";
        $result = $conn->query($sql);
        $history = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $history[] = $row;
            }
        }
        return $history;
    }
    
    private function extractNameFromEmail($email) {
        $parts = explode('@', $email);
        $name = ucfirst($parts[0]);
        return $name;
    }
    
    public function getProducts() {
        $connObj = new clsKetNoi();
        $conn = $connObj->moketnoi();
        $sql = "SELECT * FROM product";
        $result = $conn->query($sql);
        $connObj->dongketnoi();
        return $result;
    }
    
    public function getProductTypes() {
        $connObj = new clsKetNoi();
        $conn = $connObj->moketnoi();
        $sql = "SELECT * FROM typeproduct";
        $result = $conn->query($sql);
        $connObj->dongketnoi();
        return $result;
    }
    
    public function mCreateTestUsers() {
        $connObj = new clsKetNoi();
        $conn = $connObj->moketnoi();
        
        $md5Password = md5('123456');
        $currentDate = date('Y-m-d');
        
        $testUsers = array(
            array(
                'email' => 'admin_test@khachsan.com',
                'matKhau' => $md5Password,
                'loaiTaiKhoan' => 'Admin',
                'vaiTro' => 'admin',
                'trangThai' => 'HoatDong'
            ),
            array(
                'email' => 'quanly_test@khachsan.com',
                'matKhau' => $md5Password,
                'loaiTaiKhoan' => 'NhanVien',
                'vaiTro' => 'quanly',
                'trangThai' => 'HoatDong'
            ),
            array(
                'email' => 'ketoan_test@khachsan.com',
                'matKhau' => $md5Password,
                'loaiTaiKhoan' => 'NhanVien',
                'vaiTro' => 'ketoan',
                'trangThai' => 'HoatDong'
            ),
            array(
                'email' => 'letan_test@khachsan.com',
                'matKhau' => $md5Password,
                'loaiTaiKhoan' => 'NhanVien',
                'vaiTro' => 'letan',
                'trangThai' => 'HoatDong'
            ),
            array(
                'email' => 'buongphong_test@khachsan.com',
                'matKhau' => $md5Password,
                'loaiTaiKhoan' => 'NhanVien',
                'vaiTro' => 'buongphong',
                'trangThai' => 'HoatDong'
            )
        );
        
        foreach ($testUsers as $user) {
            $sql = "INSERT INTO taikhoan (email, matKhau, loaiTaiKhoan, vaiTro, trangThai, ngayTao) 
                    VALUES ('{$user['email']}', '{$user['matKhau']}', '{$user['loaiTaiKhoan']}', 
                            '{$user['vaiTro']}', '{$user['trangThai']}', '$currentDate')";
            $conn->query($sql);
        }
        
        $connObj->dongketnoi();
        return true;
    }
}
?>