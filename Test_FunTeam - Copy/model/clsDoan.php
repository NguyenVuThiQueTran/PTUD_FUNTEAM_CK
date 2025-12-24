<?php
require_once("clsconnect.php");

class clsDoan {
    private $conn;

    public function __construct() {
        $db = new clsKetNoi();
        $this->conn = $db->moketnoi();
    }

    // Kiểm tra email đã tồn tại
    public function kiemTraEmailTonTai($email) {
        $sql = "SELECT idKH FROM KhachHang WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($idKH);
        $hasResult = $stmt->fetch();
        $stmt->close();
        
        if ($hasResult) {
            return array('idKH' => $idKH);
        }
        return false;
    }

    // Tạo tài khoản cho đoàn
    public function taoTaiKhoanDoan($email, $matKhau, $maDoan) {
        try {
            $sql = "INSERT INTO TaiKhoan (email, matKhau, loaiTaiKhoan, idThamChieu, vaiTro, trangThai, ngayTao) 
                   VALUES (?, MD5(?), 'Doan', ?, 'Doan', 'HoatDong', CURDATE())";
            
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return false;
            
            // Lấy ID từ bảng Doan
            $doanId = $this->getDoanId($maDoan);
            
            $stmt->bind_param("ssi", $email, $matKhau, $doanId);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Lỗi taoTaiKhoanDoan: " . $e->getMessage());
            return false;
        }
    }

    // Lấy ID đoàn từ mã đoàn
    private function getDoanId($maDoan) {
        // Tạm thời trả về 0
        return 0;
    }

    // Thêm đoàn
    public function themDoan($maDoan, $tenDoan, $email, $matKhau, $soLuong, $thongTinLienHe) {
        try {
            $sql = "INSERT INTO Doan (maDoan, tenDoan, email, matKhau, soLuong, thongTinLienHe, vaiTro, trangThai) 
                   VALUES (?, ?, ?, MD5(?), ?, ?, 'Doan', 'HoatDong')";
            
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return false;
            
            $stmt->bind_param("ssssis", $maDoan, $tenDoan, $email, $matKhau, $soLuong, $thongTinLienHe);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Lỗi themDoan: " . $e->getMessage());
            return false;
        }
    }

    // Xóa đoàn
    public function xoaDoan($maDoan) {
        $sql = "DELETE FROM Doan WHERE maDoan = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        
        $stmt->bind_param("s", $maDoan);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    // Tạo khách hàng và tài khoản
    public function themKhachHangVaTaiKhoan($hoTen, $email, $matKhau, $soDienThoai = null, $CCCD = null, $diaChi = null, $loaiKH = 'VIP') {
        // Tắt autocommit để bắt đầu transaction
        $this->conn->autocommit(FALSE);
        
        $success = false;
        $idKH = 0;
        
        try {
            // 1. Thêm vào bảng KhachHang
            $sqlKH = "INSERT INTO KhachHang (hoTen, email, matKhau, soDienThoai, CCCD, diaChi, loaiKH, vaiTro, trangThai) 
                     VALUES (?, ?, MD5(?), ?, ?, ?, ?, 'KhachHang', 'HoatDong')";
            
            $stmtKH = $this->conn->prepare($sqlKH);
            if (!$stmtKH) {
                throw new Exception("Lỗi chuẩn bị câu lệnh KhachHang");
            }
            
            $stmtKH->bind_param("sssssss", $hoTen, $email, $matKhau, $soDienThoai, $CCCD, $diaChi, $loaiKH);
            $resultKH = $stmtKH->execute();
            $stmtKH->close();
            
            if (!$resultKH) {
                throw new Exception("Lỗi thêm KhachHang");
            }
            
            // Lấy idKH vừa tạo
            $idKH = $this->conn->insert_id;
            
            // 2. Thêm vào bảng TaiKhoan
            $sqlTK = "INSERT INTO TaiKhoan (email, matKhau, loaiTaiKhoan, idThamChieu, vaiTro, trangThai, ngayTao) 
                     VALUES (?, MD5(?), 'KhachHang', ?, 'KhachHang', 'HoatDong', CURDATE())";
            
            $stmtTK = $this->conn->prepare($sqlTK);
            if (!$stmtTK) {
                throw new Exception("Lỗi chuẩn bị câu lệnh TaiKhoan");
            }
            
            $stmtTK->bind_param("ssi", $email, $matKhau, $idKH);
            $resultTK = $stmtTK->execute();
            $stmtTK->close();
            
            if (!$resultTK) {
                throw new Exception("Lỗi thêm TaiKhoan");
            }
            
            // Commit transaction
            $this->conn->commit();
            $success = true;
            
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->conn->rollback();
            error_log("Lỗi themKhachHangVaTaiKhoan: " . $e->getMessage());
            $success = false;
            $idKH = 0;
        }
        
        // Bật lại autocommit
        $this->conn->autocommit(TRUE);
        
        return $success ? $idKH : false;
    }

    // Thêm thành viên vào đoàn
    public function themThanhVien($maDoan, $idKH, $vaiTro = 'thanhvien') {
        try {
            // Kiểm tra xem đã là thành viên chưa
            $checkSql = "SELECT idThanhVien FROM ThanhVienDoan WHERE maDoan = ? AND idKH = ?";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->bind_param("si", $maDoan, $idKH);
            $checkStmt->execute();
            $checkStmt->bind_result($idThanhVien);
            $hasResult = $checkStmt->fetch();
            $checkStmt->close();
            
            if ($hasResult) {
                return false; // Đã là thành viên
            }
            
            // Thêm thành viên mới
            $sql = "INSERT INTO ThanhVienDoan (idKH, maDoan, vaiTro) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) return false;
            
            $stmt->bind_param("iss", $idKH, $maDoan, $vaiTro);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Lỗi themThanhVien: " . $e->getMessage());
            return false;
        }
    }

    // Lấy danh sách thành viên
    public function getThanhVien($maDoan) {
        $sql = "SELECT tv.idThanhVien, tv.maDoan, tv.vaiTro, kh.idKH, kh.hoTen, kh.email, kh.soDienThoai, kh.CCCD 
                FROM ThanhVienDoan tv 
                JOIN KhachHang kh ON tv.idKH = kh.idKH 
                WHERE tv.maDoan = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return array();
        
        $stmt->bind_param("s", $maDoan);
        $stmt->execute();
        $stmt->bind_result($idThanhVien, $maDoan, $vaiTro, $idKH, $hoTen, $email, $soDienThoai, $CCCD);
        
        $thanhVien = array();
        while ($stmt->fetch()) {
            $thanhVien[] = array(
                'idThanhVien' => $idThanhVien,
                'maDoan' => $maDoan,
                'vaiTro' => $vaiTro,
                'idKH' => $idKH,
                'hoTen' => $hoTen,
                'email' => $email,
                'soDienThoai' => $soDienThoai,
                'CCCD' => $CCCD
            );
        }
        
        $stmt->close();
        return $thanhVien;
    }

    // Lấy tất cả đoàn
    public function getAllDoan() {
        $sql = "SELECT d.*, COUNT(tv.idThanhVien) as soThanhVien 
                FROM Doan d 
                LEFT JOIN ThanhVienDoan tv ON d.maDoan = tv.maDoan 
                GROUP BY d.maDoan";
        
        $result = $this->conn->query($sql);
        
        $doan = array();
        while ($row = $result->fetch_assoc()) {
            $doan[] = $row;
        }
        
        return $doan;
    }

    // Tìm kiếm đoàn
    public function timKiemDoan($keyword) {
        $sql = "SELECT d.maDoan, d.tenDoan, d.email, d.soLuong, d.thongTinLienHe, 
                       d.vaiTro, d.trangThai, COUNT(tv.idThanhVien) as soThanhVien 
                FROM Doan d 
                LEFT JOIN ThanhVienDoan tv ON d.maDoan = tv.maDoan 
                WHERE d.tenDoan LIKE ? OR d.email LIKE ? 
                GROUP BY d.maDoan";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return array();
        
        $searchTerm = "%" . $keyword . "%";
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $stmt->bind_result($maDoan, $tenDoan, $email, $soLuong, $thongTinLienHe, $vaiTro, $trangThai, $soThanhVien);
        
        $doan = array();
        while ($stmt->fetch()) {
            $doan[] = array(
                'maDoan' => $maDoan,
                'tenDoan' => $tenDoan,
                'email' => $email,
                'soLuong' => $soLuong,
                'thongTinLienHe' => $thongTinLienHe,
                'vaiTro' => $vaiTro,
                'trangThai' => $trangThai,
                'soThanhVien' => $soThanhVien
            );
        }
        
        $stmt->close();
        return $doan;
    }

    public function __destruct() {
        if($this->conn) {
            $this->conn->close();
        }
    }
}
?>