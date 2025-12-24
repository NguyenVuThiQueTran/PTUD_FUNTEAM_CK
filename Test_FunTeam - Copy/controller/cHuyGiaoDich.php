<?php
if (!class_exists('controlHuyGiaoDich')) {
    // Đảm bảo session đã start trước khi include model
    if (!isset($_SESSION)) {
        session_start();
    }

    include_once("../model/clsHuyGiaoDich.php");

    class controlHuyGiaoDich {
        
        public function __construct() {
            // Kiểm tra đăng nhập
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
        
        // Tìm kiếm đơn đặt phòng - SỬA TÊN HÀM
        public function cTimKiemDonDatPhong($keyword) {
            $p = new clsHuyGiaoDich();
            return $p->timKiemGiaoDich($keyword); // Đổi từ timKiemDonDatPhong sang timKiemGiaoDich
        }
        
        // Lấy chi tiết đơn đặt phòng
        public function cLayChiTietDonDatPhong($maDDP) {
            $p = new clsHuyGiaoDich();
            return $p->layChiTietDonDatPhong($maDDP);
        }
        
        // Hủy đơn đặt phòng
        public function cHuyDonDatPhong($maDDP) {
            $p = new clsHuyGiaoDich();
            
            // 1. Lấy thông tin đơn trước khi kiểm tra
            $chiTietDon = $p->layChiTietDonDatPhong($maDDP);
            
            if (!$chiTietDon || empty($chiTietDon['donDatPhong'])) {
                echo '<script>
                    alert("Không tìm thấy thông tin đơn đặt phòng!");
                    history.back();
                </script>';
                exit();
            }
            
            $trangThai = $chiTietDon['donDatPhong']['trangThai'];
            $ngayNhanPhong = $chiTietDon['donDatPhong']['ngayNhanPhong'];
            $ngayHienTai = date('Y-m-d');
            
            // 2. Kiểm tra kỹ hơn
            if ($trangThai == 'DaHuy') {
                echo '<script>
                    alert("ĐƠN NÀY ĐÃ BỊ HỦY TRƯỚC ĐÓ!\\nKhông thể hủy lại.");
                    window.location.href = "../view/huygiaodich.php";
                </script>';
                exit();
            }
            
            // Kiểm tra ngày nhận phòng
            if (strtotime($ngayNhanPhong) <= strtotime($ngayHienTai)) {
                echo '<script>
                    alert("KHÔNG THỂ HỦY!\\nĐã đến hoặc quá ngày nhận phòng.");
                    history.back();
                </script>';
                exit();
            }
            
            // 3. Thực hiện hủy
            if ($p->huyDonDatPhong($maDDP)) {
                // SỬA: Thêm chữ "Hủy" vào message
                $message = "Hủy đơn đặt phòng thành công!";
                echo '<script>
                    alert("' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '");
                    window.location.href = "../view/huygiaodich.php";
                </script>';
                exit();
            } else {
                echo '<script>
                    alert("Lỗi khi hủy đơn đặt phòng!");
                    history.back();
                </script>';
                exit();
            }
        }
        
        // Kiểm tra trạng thái hủy
        public function cKiemTraCoTheHuy($maDDP) {
            $p = new clsHuyGiaoDich();
            return $p->kiemTraCoTheHuy($maDDP);
        }
    }
}
?>