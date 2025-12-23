<?php
if (!class_exists('controlTraPhong')) {
    if (!isset($_SESSION)) {
        session_start();
    }
    include_once("../model/clsTraPhong.php");

    class controlTraPhong {
        
        public function __construct() {
            $this->kiemTraDangNhap();
        }
        
        private function kiemTraDangNhap() {
            if (!isset($_SESSION["dn"]) || $_SESSION["dn"] !== true) {
                echo '<script>
                    alert("Vui lòng đăng nhập!");
                    window.location.href = "../view/login.php";
                </script>';
                exit();
            }
        }
        
        // Tìm kiếm giao dịch
        public function cTimKiemGiaoDich($keyword) {
            $p = new clsTraPhong();
            return $p->timKiemGiaoDich($keyword);
        }
        
        // Lấy chi tiết giao dịch
        public function cLayChiTietGiaoDich($maDDP) {
            $p = new clsTraPhong();
            return $p->layChiTietGiaoDich($maDDP);
        }
        
        // Xác nhận trả phòng
        public function cXacNhanTraPhong($maDDP, $phuongThucTT, $noiDungChuyenKhoan = '') {
            $p = new clsTraPhong();
            
            // Kiểm tra có thể trả phòng không
            if (!$p->kiemTraCoTheTraPhong($maDDP)) {
                echo '<script>
                    alert("Đơn đặt phòng không tồn tại hoặc không ở trạng thái \'Đã nhận phòng\'!");
                    history.back();
                </script>';
                exit();
            }
            
            // Kiểm tra phương thức thanh toán
            if ($phuongThucTT == 'Chuyển khoản' && empty($noiDungChuyenKhoan)) {
                echo '<script>
                    alert("Vui lòng nhập nội dung chuyển khoản!");
                    history.back();
                </script>';
                exit();
            }
            
            // Thực hiện trả phòng
            if ($p->xacNhanTraPhong($maDDP, $phuongThucTT, $noiDungChuyenKhoan)) {
                $message = "Trả phòng thành công! Đã cập nhật trạng thái đơn và phòng.";
                echo '<script>
                    alert("' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '");
                    window.location.href = "../view/traphong.php";
                </script>';
                exit();
            } else {
                echo '<script>
                    alert("Lỗi khi trả phòng. Vui lòng thử lại!");
                    history.back();
                </script>';
                exit();
            }
        }
        
        // Kiểm tra có thể trả phòng
        public function cKiemTraCoTheTraPhong($maDDP) {
            $p = new clsTraPhong();
            return $p->kiemTraCoTheTraPhong($maDDP);
        }
        
        // Lấy thông tin thanh toán ngân hàng
        public function cLayThongTinNganHang() {
            return array(
                'nganHang' => 'Vietcombank',
                'soTaiKhoan' => '0123456789',
                'chuTaiKhoan' => 'CÔNG TY TNHH KHÁCH SẠN FUNTEAM',
                'chiNhanh' => 'Chi nhánh TP.HCM'
            );
        }
    }
}
?>