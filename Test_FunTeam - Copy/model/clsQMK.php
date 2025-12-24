<?php
require_once("clsconnect.php");

class clsQMK extends clsKetNoi {

    public function kiemTraEmail($email) {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $link = $this->moketnoi();
        $sql = "SELECT idUser FROM taikhoan WHERE email = ? LIMIT 1";
        $stmt = $link->prepare($sql);

        if ($stmt === false) return false;

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Tạo mật khẩu ngẫu nhiên
    private function taoMatKhauNgauNhien($length = 10) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {

            // random_int PHP 7+
            if (function_exists('random_int')) {
                $idx = random_int(0, $maxIndex);
            }
            // random_bytes
            elseif (function_exists('random_bytes')) {
                $bytes = random_bytes(4);
                $arr = unpack('L', $bytes);
                $val = $arr[1];       // Không dùng cú pháp []
                $idx = $val % ($maxIndex + 1);
            }
            // openssl_random_pseudo_bytes
            elseif (function_exists('openssl_random_pseudo_bytes')) {
                $bytes = openssl_random_pseudo_bytes(4);
                $arr = unpack('L', $bytes);
                $val = $arr[1];
                $idx = $val % ($maxIndex + 1);
            }
            // fallback cuối
            else {
                $idx = mt_rand(0, $maxIndex);
            }

            $password .= $chars[$idx];
        }

        return $password;
    }

    public function guiMatKhauMoi($email) {
        // Tạo mật khẩu mới bằng hàm bạn đã viết
        $newPass = $this->taoMatKhauNgauNhien(10);
        $hashed = md5($newPass);

        $conn = $this->moketnoi();
        if (!$conn) {
            return array(
                "status" => false,
                "message" => "Không thể kết nối database!"
            );
        }

        $sql = "UPDATE taikhoan SET matKhau = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return array(
                "status" => false,
                "message" => "Lỗi chuẩn bị câu lệnh SQL!"
            );
        }

        $stmt->bind_param("ss", $hashed, $email);

        if ($stmt->execute()) {
    // LUÔN DÙNG FAKE EMAIL
    $guiEmailThanhCong = $this->guiEmailMatKhau($email, $newPass);
    
    if ($guiEmailThanhCong) {
        return array(
            "status" => true,
            "message" => "Mật khẩu mới đã được gửi đến email của bạn!",
            "newpass" => $newPass // HIỂN THỊ MẬT KHẨU
        );
    }
}

        return array(
            "status" => false,
            "message" => "Không thể cập nhật mật khẩu!"
        );
    }

    // Hàm gửi email PHPMailer (nếu muốn dùng thật)
    private function guiEmailMatKhau($email, $matKhauMoi) {
    // FAKE EMAIL - LUÔN THÀNH CÔNG
    error_log("✅ FAKE EMAIL: {$email} - PASSWORD: {$matKhauMoi}");
    return true;
}

    // Hàm fake gửi email cho development - DÙNG CÁI NÀY CHO CHẮC
    private function guiEmailMatKhau_Fake($email, $matKhauMoi) {
        // Luôn trả về true để test
        error_log("🎯 FAKE EMAIL SENT TO: " . $email);
        error_log("🔐 PASSWORD: " . $matKhauMoi);
        return true;
    }
}
?>