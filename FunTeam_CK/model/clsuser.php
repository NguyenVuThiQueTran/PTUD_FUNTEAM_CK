<?php
require_once("clsconnect.php");

class clsUser {
    private $conn;

    public function __construct() {
        $db = new clsKetNoi();
        $this->conn = $db->moketnoi();
    }

    public function kiemTraDangNhap($email, $matKhau) {
        $sql = "SELECT idUser, email, vaiTro, loaiTaiKhoan, idThamChieu 
                FROM TaiKhoan 
                WHERE email = ? AND matKhau = MD5(?) AND trangThai = 'HoatDong' 
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        
        $stmt->bind_param("ss", $email, $matKhau);
        $stmt->execute();
        $stmt->bind_result($idUser, $email, $vaiTro, $loaiTaiKhoan, $idThamChieu);
        $hasResult = $stmt->fetch();
        $stmt->close();
        
        if ($hasResult) {
            return array(
                'idUser' => $idUser,
                'email' => $email,
                'vaiTro' => $vaiTro,
                'loaiTaiKhoan' => $loaiTaiKhoan,
                'idThamChieu' => $idThamChieu
            );
        }
        return false;
    }

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

    public function themKhachHangVaTaiKhoan($hoTen, $email, $matKhau, $soDienThoai = null, $CCCD = null, $diaChi = null, $loaiKH = 'Thuong') {
        $this->conn->autocommit(FALSE);
        
        $success = false;
        $idKH = 0;
        
        try {
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
            
            $idKH = $this->conn->insert_id;

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

            $this->conn->commit();
            $success = true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Lỗi themKhachHangVaTaiKhoan: " . $e->getMessage());
            $success = false;
            $idKH = 0;
        }
        
        $this->conn->autocommit(TRUE);
        
        return $success ? $idKH : false;
    }

    public function cRegister($email, $matKhau, $hoTen, $CCCD) {
        return $this->themKhachHangVaTaiKhoan($hoTen, $email, $matKhau, null, $CCCD);
    }

    public function __destruct() {
        if($this->conn) {
            $this->conn->close();
        }
    }
}
?>