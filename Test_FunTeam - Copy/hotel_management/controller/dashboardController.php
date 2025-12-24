<?php
// controller/dashboardController.php
// Controller để xử lý dữ liệu cho dashboard

require_once dirname(__FILE__) . '/../config/database.php';

class DashboardController {
    
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy dữ liệu doanh thu theo tháng trong năm
     */
    public function getRevenueByMonth() {
        // Return revenue for the last 12 months (including current month)
        $data = array();
        $months = array();
        $now = new DateTime();
        for ($i = 11; $i >= 0; $i--) {
            $m = clone $now;
            $m->modify("-{$i} months");
            $key = $m->format('Y-m');
            $months[] = $key;
            $data[$key] = 0.0;
        }

        try {
            // Aggregate by Year-Month using paid date (ngayThanhToan) or fallback to ngayLap
            $sql = "SELECT DATE_FORMAT(COALESCE(ngayThanhToan, ngayLap), '%Y-%m') AS ym, SUM(COALESCE(tongTien,0)) AS total
                    FROM hoadon
                    WHERE DATE_FORMAT(COALESCE(ngayThanhToan, ngayLap), '%Y-%m') IN (" . implode(',', array_fill(0, count($months), '?')) . ")
                    AND (ngayThanhToan IS NOT NULL OR trangThai LIKE '%Da%' OR trangThai LIKE '%Đã%')
                    GROUP BY ym";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($months);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $r) {
                if (isset($r['ym']) && isset($data[$r['ym']])) {
                    $data[$r['ym']] = (float)$r['total'];
                }
            }

            // If hoadon had no data for these months, fallback to datphong sums for the same months
            $hasAny = array_sum($data) > 0;
            if (!$hasAny) {
                $sql2 = "SELECT DATE_FORMAT(NgayDat, '%Y-%m') AS ym, SUM(p.giaPhong) AS total
                         FROM datphong dp
                         JOIN phong p ON dp.MaPhong = p.MaPhong
                         WHERE DATE_FORMAT(NgayDat, '%Y-%m') IN (" . implode(',', array_fill(0, count($months), '?')) . ")
                         GROUP BY ym";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->execute($months);
                $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows2 as $r) {
                    if (isset($r['ym']) && isset($data[$r['ym']])) {
                        $data[$r['ym']] = (float)$r['total'];
                    }
                }
            }

            // Return simple 0-indexed numeric array matching months labels
            $out = array();
            foreach ($months as $k) $out[] = $data[$k];

            // If no data in the last 12 months, try previous calendar year (useful for test/import data)
            $sum = array_sum($out);
            if ($sum <= 0) {
                $prevYear = (int)date('Y') - 1;
                try {
                    $sqlY = "SELECT MONTH(COALESCE(ngayThanhToan, ngayLap)) AS m, SUM(COALESCE(tongTien,0)) AS total
                             FROM hoadon
                             WHERE YEAR(COALESCE(ngayThanhToan, ngayLap)) = ?
                             AND (ngayThanhToan IS NOT NULL OR trangThai LIKE '%Da%' OR trangThai LIKE '%Đã%')
                             GROUP BY MONTH(COALESCE(ngayThanhToan, ngayLap))";
                    $s = $this->conn->prepare($sqlY);
                    $s->execute(array($prevYear));
                    $rowsY = $s->fetchAll(PDO::FETCH_ASSOC);
                    $outY = array_fill(0, 12, 0.0);
                    foreach ($rowsY as $r) {
                        $m = (int)$r['m'];
                        if ($m >=1 && $m <= 12) $outY[$m-1] = (float)$r['total'];
                    }
                    // if prev year has data, return it
                    if (array_sum($outY) > 0) return $outY;
                } catch (Exception $e) {
                    // ignore and fallback to zeros
                }
            }

            return $out;
        } catch (Exception $e) {
            error_log("getRevenueByMonth error: " . $e->getMessage());
            // return zeros for 12 months
            return array_fill(0, 12, 0.0);
        }
    }

    /**
     * Lấy tỷ lệ lấp đầy phòng
     */
    // Normalize legacy statuses (idempotent) and compute occupancy breakdown
    public function getOccupancyRate() {
        try {
            // Check for legacy/variant status values in DB and normalize them once per call (safe)
            $legacyFound = false;
            $stmt = $this->conn->query("SELECT DISTINCT TrangThai FROM phong");
            $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            $mapOccupied = array('Đã thuê','Có khách','Đang sử dụng','Da nhan','Co khach','Dang su dung','Đã nhận phòng','Da nhan phong');
            $mapCleaning = array('Dọn dẹp','Đang dọn','Don dep','Dang don');
            $mapMaintenance = array('Bảo trì');
            $mapReserved = array('Đã đặt','Da dat','Dat');

            foreach($statuses as $s) {
                if(in_array($s, $mapOccupied) || in_array($s, $mapCleaning) || in_array($s, $mapMaintenance) || in_array($s, $mapReserved)){
                    $legacyFound = true; break;
                }
            }

            if($legacyFound){
                try {
                    // Occupied -> 'Đang ở'
                    $q1 = "UPDATE phong SET TrangThai = 'Đang ở' WHERE TrangThai IN ('" . implode("','", $mapOccupied) . "')";
                    $this->conn->exec($q1);
                    // Cleaning-like -> 'Bảo trì'
                    $q2 = "UPDATE phong SET TrangThai = 'Bảo trì' WHERE TrangThai IN ('" . implode("','", $mapCleaning) . "')";
                    $this->conn->exec($q2);
                    // Reserved -> 'Đã đặt'
                    $q3 = "UPDATE phong SET TrangThai = 'Đã đặt' WHERE TrangThai IN ('" . implode("','", $mapReserved) . "')";
                    $this->conn->exec($q3);
                } catch(Exception $e) {
                    error_log('Status normalization failed: ' . $e->getMessage());
                }
            }

            // Compute explicit counts
            $sql = "SELECT 
                        SUM(CASE WHEN TrangThai = 'Đang ở' THEN 1 ELSE 0 END) AS dang_o,
                        SUM(CASE WHEN TrangThai = 'Đã đặt' THEN 1 ELSE 0 END) AS da_dat,
                        SUM(CASE WHEN TrangThai = 'Trống' THEN 1 ELSE 0 END) AS trong,
                        SUM(CASE WHEN TrangThai = 'Bảo trì' THEN 1 ELSE 0 END) AS bao_tri,
                        COUNT(*) AS total
                    FROM phong";
            $stmt = $this->conn->query($sql);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $dang_o = (int)(isset($row['dang_o']) ? $row['dang_o'] : 0);
            $da_dat = (int)(isset($row['da_dat']) ? $row['da_dat'] : 0);
            $trong = (int)(isset($row['trong']) ? $row['trong'] : 0);
            $bao_tri = (int)(isset($row['bao_tri']) ? $row['bao_tri'] : 0);
            $total = (int)(isset($row['total']) ? $row['total'] : 0);

            return array(
                'total' => $total,
                'dang_o' => $dang_o,
                'da_dat' => $da_dat,
                'trong' => $trong,
                'bao_tri' => $bao_tri
            );
        } catch (Exception $e) {
            error_log("getOccupancyRate error: " . $e->getMessage());
            return array('total' => 0, 'dang_o' => 0, 'da_dat' => 0, 'trong' => 0, 'bao_tri' => 0);
        }
    }

    /**
     * Debug helper: return raw counts from DB to assist troubleshooting
     */
    public function getRawCounts() {
        try {
            $r_phong = $this->conn->query("SELECT COUNT(*) AS c FROM phong")->fetch(PDO::FETCH_ASSOC);
            $r_hoadon = $this->conn->query("SELECT COUNT(*) AS c FROM hoadon")->fetch(PDO::FETCH_ASSOC);
            $stmt = $this->conn->prepare("SELECT COUNT(*) AS c FROM hoadon WHERE YEAR(COALESCE(ngayThanhToan, ngayLap)) = ?");
            $stmt->execute(array((int)date('Y')-1));
            $r_hoadon_prev = $stmt->fetch(PDO::FETCH_ASSOC);
            return array(
                'phong' => isset($r_phong['c']) ? (int)$r_phong['c'] : 0,
                'hoadon_total' => isset($r_hoadon['c']) ? (int)$r_hoadon['c'] : 0,
                'hoadon_prev_year' => isset($r_hoadon_prev['c']) ? (int)$r_hoadon_prev['c'] : 0
            );
        } catch (Exception $e) {
            error_log('getRawCounts error: ' . $e->getMessage());
            return array('phong'=>0,'hoadon_total'=>0,'hoadon_prev_year'=>0);
        }
    }

    /**
     * Lấy danh sách đặt phòng gần đây
     */
    public function getRecentBookings($limit = 10) {
        try {
            $stmt = $this->conn->query(
                "SELECT dp.MaDP, kh.HoTen, p.SoPhong, p.HangPhong, dp.NgayNhan, dp.NgayTra, dp.TrangThai, kh.DienThoai
                FROM datphong dp
                JOIN khachhang kh ON dp.MaKH = kh.MaKH
                JOIN phong p ON dp.MaPhong = p.MaPhong
                ORDER BY dp.NgayDat DESC
                LIMIT $limit"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("getRecentBookings error: " . $e->getMessage());
            return array();
        }
    }

    /**
     * Lấy danh sách Check-in/Check-out hôm nay
     */
    public function getTodayChecks() {
        try {
            $today = date('Y-m-d');
            
            // Check-in hôm nay
            $stmtCheckIn = $this->conn->query(
                "SELECT dp.MaDP, kh.HoTen, p.SoPhong, p.HangPhong, dp.NgayNhan, 'Check-in' as loai
                FROM datphong dp
                JOIN khachhang kh ON dp.MaKH = kh.MaKH
                JOIN phong p ON dp.MaPhong = p.MaPhong
                WHERE dp.NgayNhan = '$today'
                ORDER BY dp.NgayNhan"
            );
            $checkIns = $stmtCheckIn->fetchAll(PDO::FETCH_ASSOC);
            
            // Check-out hôm nay
            $stmtCheckOut = $this->conn->query(
                "SELECT dp.MaDP, kh.HoTen, p.SoPhong, p.HangPhong, dp.NgayTra, 'Check-out' as loai
                FROM datphong dp
                JOIN khachhang kh ON dp.MaKH = kh.MaKH
                JOIN phong p ON dp.MaPhong = p.MaPhong
                WHERE dp.NgayTra = '$today'
                ORDER BY dp.NgayTra"
            );
            $checkOuts = $stmtCheckOut->fetchAll(PDO::FETCH_ASSOC);
            
            return array(
                'checkIn' => $checkIns,
                'checkOut' => $checkOuts
            );
        } catch (Exception $e) {
            error_log("getTodayChecks error: " . $e->getMessage());
            return array('checkIn' => array(), 'checkOut' => array());
        }
    }

    /**
     * Lấy số liệu thống kê cơ bản
     */
    public function getBasicStats() {
        try {
            // Tổng phòng
            $stmtPhong = $this->conn->query("SELECT COUNT(*) as total FROM phong");
            $totalPhong = $stmtPhong->fetch(PDO::FETCH_ASSOC);
            $totalPhong = isset($totalPhong['total']) ? $totalPhong['total'] : 0;
            
            // Tổng khách hàng
            $stmtKH = $this->conn->query("SELECT COUNT(*) as total FROM khachhang");
            $totalKH = $stmtKH->fetch(PDO::FETCH_ASSOC);
            $totalKH = isset($totalKH['total']) ? $totalKH['total'] : 0;
            
            // Tổng đặt phòng hôm nay
            $today = date('Y-m-d');
            $stmtDPHom = $this->conn->query("SELECT COUNT(*) as total FROM datphong WHERE NgayDat = '$today'");
            $dpHom = $stmtDPHom->fetch(PDO::FETCH_ASSOC);
            $dpHom = isset($dpHom['total']) ? $dpHom['total'] : 0;
            
            // Doanh thu hôm nay
            $stmtDoanhThuHom = $this->conn->query(
                "SELECT COALESCE(SUM(p.DonGia), 0) as tong
                FROM datphong dp
                JOIN phong p ON dp.MaPhong = p.MaPhong
                WHERE DATE(dp.NgayNhan) = '$today'"
            );
            $doanhThuHom = $stmtDoanhThuHom->fetch(PDO::FETCH_ASSOC);
            $doanhThuHom = isset($doanhThuHom['tong']) ? $doanhThuHom['tong'] : 0;
            
            return array(
                'totalPhong' => $totalPhong,
                'totalKH' => $totalKH,
                'dpHom' => $dpHom,
                'doanhThuHom' => $doanhThuHom
            );
        } catch (Exception $e) {
            error_log("getBasicStats error: " . $e->getMessage());
            return array('totalPhong' => 0, 'totalKH' => 0, 'dpHom' => 0, 'doanhThuHom' => 0);
        }
    }

    /**
     * Lấy danh sách phòng đang có khách (hiện đang đặt phòng)
     */
    public function getCurrentOccupiedRooms($limit = 10) {
        try {
            $today = date('Y-m-d');
            $stmt = $this->conn->query(
                "SELECT dp.MaDP, kh.HoTen, p.SoPhong, p.HangPhong, dp.NgayNhan, dp.NgayTra, dp.TrangThai, kh.DienThoai
                FROM datphong dp
                JOIN khachhang kh ON dp.MaKH = kh.MaKH
                JOIN phong p ON dp.MaPhong = p.MaPhong
                WHERE dp.NgayNhan <= '$today' AND dp.NgayTra >= '$today'
                AND (
                    dp.TrangThai = 'Đã nhận phòng' OR dp.TrangThai = 'Có khách' OR dp.TrangThai = 'Đang sử dụng'
                    OR dp.TrangThai = 'Da nhan phong' OR dp.TrangThai = 'Co khach' OR dp.TrangThai = 'Dang su dung'
                )
                ORDER BY dp.NgayNhan DESC
                LIMIT $limit"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("getCurrentOccupiedRooms error: " . $e->getMessage());
            return array();
        }
    }
}
