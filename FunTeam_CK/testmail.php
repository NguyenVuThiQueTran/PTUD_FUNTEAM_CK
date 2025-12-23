<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Cấu hình SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ten_gmail_cua_em@gmail.com'; // Gmail gửi đi
    $mail->Password   = 'mã_ứng_dụng_gmail'; // không phải mật khẩu Gmail!
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Người gửi & người nhận
    $mail->setFrom('ten_gmail_cua_em@gmail.com', 'Hệ thống PTUD');
    $mail->addAddress('nguoi_nhan@gmail.com', 'Người nhận');

    // Nội dung
    $mail->isHTML(true);
    $mail->Subject = 'Thử gửi mail bằng PHPMailer';
    $mail->Body    = '<h3>Gửi mail thành công!</h3><p>Đây là mail test từ hệ thống của bạn.</p>';

    $mail->send();
    echo '✅ Gửi mail thành công!';
} catch (Exception $e) {
    echo "❌ Gửi mail thất bại. Lỗi: {$mail->ErrorInfo}";
}
?>
