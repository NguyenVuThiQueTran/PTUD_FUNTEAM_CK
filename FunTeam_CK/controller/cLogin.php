<?php 
include_once("../model/clslogin.php"); 

class controlUser { 
    public function cRegis($email, $Pwd) { 
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); 
        } 
        $p = new nodeUser(); 
        $Pwd = md5($Pwd); 
        $chk = $p->mCheckEmail($email); 
        
        if ($chk->num_rows > 0) { 
            echo '<meta charset="UTF-8">'; 
            echo "<script>alert('Tài khoản đã tồn tại!');</script>"; 
            exit(); 
        } else { 
            $result = $p->mRegis($email, $Pwd); 
            if ($result) { 
                echo '<meta charset="UTF-8">'; 
                echo "<script>alert('Đăng ký thành công!'); window.location.href='index.php';</script>"; 
                exit(); 
            } else { 
                echo '<meta charset="UTF-8">'; 
                echo "<script>alert('Lỗi khi đăng ký!');</script>"; 
                exit(); 
            } 
        } 
    }
    
    public function cLogin($email, $Pwd) {
        if(session_id() == '') {
            session_start();
        }

        $p = new nodeUser();

        // Note: removed test shortcut that logged in any user with password "1111"
        // If you need a test/login shortcut, implement a proper dev-only flag.

        // Mã hóa mật khẩu để so sánh với database
        $md5Password = md5($Pwd);
        
        // Lấy thông tin user với phân quyền
        $userInfo = $p->mGetUserRoleInfo($email, $md5Password);
        
        if ($userInfo) {
            // DEBUG: Hiển thị thông tin user
            echo "<div style='background:#f0f0f0; padding:20px; margin:20px; border:1px solid #ccc;'>";
            echo "<h3>DEBUG - Thông tin user từ database:</h3>";
            echo "<pre>";
            print_r($userInfo);
            echo "</pre>";
            echo "</div>";
            
            // Lưu thông tin session
            $_SESSION["dn"] = true;
            $_SESSION["user_id"] = $userInfo['idUser'];
            $_SESSION["email"] = $email;
            
            // Xác định username
            if (!empty($userInfo['khHoTen'])) {
                $_SESSION["username"] = $userInfo['khHoTen'];
            } elseif (!empty($userInfo['nsHoTen'])) {
                $_SESSION["username"] = $userInfo['nsHoTen'];
            } elseif (!empty($userInfo['tenDoan'])) {
                $_SESSION["username"] = $userInfo['tenDoan'];
            } else {
                $_SESSION["username"] = $email;
            }
            
            // Lưu role
            if (isset($userInfo['user_role']) && !empty($userInfo['user_role'])) {
                $_SESSION["role"] = $userInfo['user_role'];
            } elseif (isset($userInfo['loaiTaiKhoan']) && !empty($userInfo['loaiTaiKhoan'])) {
                $_SESSION["role"] = $userInfo['loaiTaiKhoan'];
            } else {
                $_SESSION["role"] = 'khachhang';
            }
            
            // DEBUG: Hiển thị session sẽ lưu
            echo "<div style='background:#e0ffe0; padding:20px; margin:20px; border:1px solid #0c0;'>";
            echo "<h3>DEBUG - Session sẽ được lưu:</h3>";
            echo "Role: " . $_SESSION["role"] . "<br>";
            echo "Username: " . $_SESSION["username"] . "<br>";
            echo "LoaiTaiKhoan: " . (isset($userInfo['loaiTaiKhoan']) ? $userInfo['loaiTaiKhoan'] : 'N/A') . "<br>";
            echo "VaiTro: " . (isset($userInfo['vaiTro']) ? $userInfo['vaiTro'] : 'N/A') . "<br>";
            echo "User Role từ query: " . (isset($userInfo['user_role']) ? $userInfo['user_role'] : 'N/A') . "<br>";
            echo "</div>";
            
            // Lưu thông tin bổ sung
            if ($userInfo['loaiTaiKhoan'] == 'KhachHang') {
                $_SESSION["idKH"] = $userInfo['idKH'];
                $_SESSION["loaiKH"] = (isset($userInfo['loaiKH']) && $userInfo['loaiKH']) ? $userInfo['loaiKH'] : 'Thuong';
            } elseif ($userInfo['loaiTaiKhoan'] == 'Doan') {
                $_SESSION["maDoan"] = $userInfo['maDoan'];
            } elseif ($userInfo['loaiTaiKhoan'] == 'NhanVien') {
                $_SESSION["maNS"] = $userInfo['maNS'];
            }
            
            // Chuyển hướng
            $this->redirectToDashboard($_SESSION["role"]);
            
        } else {
    echo '<meta charset="UTF-8">';
    echo "<script>
            alert('Email hoặc mật khẩu không đúng!');
            window.location.href = 'login.php'; // hoặc tên file đăng nhập thực tế
          </script>";
    return false;
}
    }
    
    private function redirectToDashboard($role) {
        $message = $this->getWelcomeMessage($role);
        
        echo '<meta charset="UTF-8">';
        echo "<script>
                alert('Đăng nhập thành công!\\\\n{$message}');
                window.location.href='dashboard.php';
              </script>";
        exit();
    }
    
    private function getWelcomeMessage($role) {
        switch($role) {
            case 'admin': return 'Chào mừng Quản trị viên!';
            case 'quanly': return 'Chào mừng Quản lý!';
            case 'ketoan': return 'Chào mừng Kế toán!';
            case 'letan': return 'Chào mừng Lễ tân!';
            case 'buongphong': return 'Chào mừng Nhân viên buồng phòng!';
            case 'doan': return 'Chào mừng Đoàn khách!';
            default: return 'Chào mừng Khách hàng!';
        }
    }
    
    public function cLogout() {
        if(session_id() == '') {
            session_start();
        }
        
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        header("Location: index.php");
        exit();
    }
    
    // Hàm để test tạo user
    public function cCreateTestUsers() {
        $p = new nodeUser();
        $result = $p->mCreateTestUsers();
        
        if ($result) {
            echo '<meta charset="UTF-8">';
            echo "<script>alert('Đã tạo tài khoản test thành công!'); window.location.href='index.php';</script>";
        } else {
            echo '<meta charset="UTF-8">';
            echo "<script>alert('Lỗi khi tạo tài khoản test!');</script>";
        }
        exit();
    }
} 
?>