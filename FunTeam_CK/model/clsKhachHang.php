<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("clsconnect.php");

class clsKhachHang {
    private $conn;

    public function __construct() {
        $db = new clsKetNoi();
        $this->conn = $db->moketnoi();
    }

    // Kiểm tra email đã tồn tại trong cả 2 bảng
    public function kiemTraEmailTonTai($email) {
        $sql = "SELECT idKH FROM KhachHang WHERE email = ? 
                UNION 
                SELECT idUser FROM TaiKhoan WHERE email = ? 
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $stmt->bind_result($id);
        $hasResult = $stmt->fetch();
        $stmt->close();
        
        return $hasResult;
    }

    // Đăng ký khách hàng - lưu vào cả 2 bảng
    public function dangKyKhachHang($hoTen, $email, $soDienThoai, $CCCD, $diaChi, $matKhau, $loaiKH = 'Thuong') {
        // Kiểm tra email đã tồn tại
        if ($this->kiemTraEmailTonTai($email)) {
            return array(
                'success' => false,
                'message' => 'Email đã được đăng ký trong hệ thống!'
            );
        }
        
        // Bắt đầu transaction
        $this->conn->autocommit(FALSE);
        
        $result = null;
        
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
                throw new Exception("Lỗi thêm KhachHang vào database!");
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
                throw new Exception("Lỗi thêm TaiKhoan vào database!");
            }
            
            // Commit transaction
            $this->conn->commit();
            
            $result = array(
                'success' => true,
                'message' => 'Đăng ký khách hàng thành công!',
                'idKH' => $idKH,
                'email' => $email
            );
            
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->conn->rollback();
            error_log("Lỗi dangKyKhachHang: " . $e->getMessage());
            
            $result = array(
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            );
        }
        
        // Bật lại autocommit
        $this->conn->autocommit(TRUE);
        
        return $result;
    }

    // Đăng nhập khách hàng (có thể đăng nhập qua cả 2 bảng)
    public function dangNhap($email, $matKhau) {
        // Thử đăng nhập qua bảng TaiKhoan trước
        $sql = "SELECT tk.idUser, tk.email, tk.vaiTro, tk.trangThai, tk.idThamChieu as idKH, 
                       kh.hoTen, kh.soDienThoai
                FROM TaiKhoan tk
                LEFT JOIN KhachHang kh ON tk.idThamChieu = kh.idKH
                WHERE tk.email = ? AND tk.matKhau = MD5(?) AND tk.loaiTaiKhoan = 'KhachHang' 
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return array('success' => false, 'error' => 'Lỗi hệ thống');
        }
        
        $stmt->bind_param("ss", $email, $matKhau);
        $stmt->execute();
        $stmt->bind_result($idUser, $email, $vaiTro, $trangThai, $idKH, $hoTen, $soDienThoai);
        $hasResult = $stmt->fetch();
        $stmt->close();
        
        if ($hasResult) {
            if ($trangThai == 'Khoa') {
                return array('success' => false, 'error' => 'Tài khoản đã bị khóa!');
            }
            
            // Lưu thông tin vào session (đồng bộ với các view/controller khác)
            session_start();
            $_SESSION['login'] = true;
            $_SESSION['dn'] = true; // flag đăng nhập chung
            $_SESSION['email'] = $email;
            $_SESSION['vaiTro'] = $vaiTro;
            $_SESSION['hoTen'] = $hoTen;
            $_SESSION['username'] = $hoTen; // tên hiển thị chung
            $_SESSION['role'] = 'khachhang';
            $_SESSION['idKH'] = $idKH;
            $_SESSION['soDienThoai'] = $soDienThoai;
            $_SESSION['user_id'] = $idUser;
            
            return array('success' => true);
        } else {
            // Nếu không tìm thấy trong TaiKhoan, thử tìm trong KhachHang
            return $this->dangNhapTuKhachHang($email, $matKhau);
        }
    }

    // Đăng nhập từ bảng KhachHang
    public function dangNhapTuKhachHang($email, $matKhau) {
        $sql = "SELECT idKH, hoTen, email, soDienThoai, vaiTro, trangThai
                FROM KhachHang 
                WHERE email = ? AND matKhau = MD5(?) 
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return array('success' => false, 'error' => 'Lỗi hệ thống');
        }
        
        $stmt->bind_param("ss", $email, $matKhau);
        $stmt->execute();
        $stmt->bind_result($idKH, $hoTen, $email, $soDienThoai, $vaiTro, $trangThai);
        $hasResult = $stmt->fetch();
        $stmt->close();
        
        if ($hasResult) {
            if ($trangThai == 'Khoa') {
                return array('success' => false, 'error' => 'Tài khoản đã bị khóa!');
            }
            
            // Lưu thông tin vào session (đồng bộ với các view/controller khác)
            session_start();
            $_SESSION['login'] = true;
            $_SESSION['dn'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['vaiTro'] = $vaiTro;
            $_SESSION['hoTen'] = $hoTen;
            $_SESSION['username'] = $hoTen; // tên hiển thị chung
            $_SESSION['role'] = 'khachhang';
            $_SESSION['idKH'] = $idKH;
            $_SESSION['soDienThoai'] = $soDienThoai;
            $_SESSION['user_id'] = $idKH; // Dùng idKH thay cho idUser
            
            return array('success' => true);
        } else {
            return array('success' => false, 'error' => 'Email hoặc mật khẩu không đúng!');
        }
    }

    public function __destruct() {
        if($this->conn) {
            $this->conn->close();
        }
    }
}
?>