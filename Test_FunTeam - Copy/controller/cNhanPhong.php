<?php
if (!class_exists('controlNhanPhong')) {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    include_once("../model/clsNhanPhong.php");

    class controlNhanPhong {
        
        public function __construct() {
            $this->kiemTraDangNhap();
        }
        
        private function kiemTraDangNhap() {
            if (!isset($_SESSION["dn"]) || $_SESSION["dn"] !== true) {
                header("Location: ../view/login.php");
                exit();
            }
        }
        
        // Tìm kiếm giao dịch
        public function cTimKiemGiaoDich($keyword) {
            $p = new clsNhanPhong();
            return $p->timKiemGiaoDich($keyword);
        }
        // Thêm hàm này vào class controlNhanPhong
        public function cTinhTienPhong($maDDP) {
            $p = new clsNhanPhong();
            return $p->tinhTienPhong($maDDP);
        }
        public function cLayDanhSachPhong($maDDP) {
                $p = new clsNhanPhong();
                return $p->layDanhSachPhong($maDDP);
            }
        // Lấy chi tiết giao dịch
        public function cLayChiTietGiaoDich($maDDP) {
            $p = new clsNhanPhong();
            return $p->layChiTietGiaoDich($maDDP);
        }
        
        // Kiểm tra thời gian nhận phòng
        public function cKiemTraThoiGian($maDDP) {
            $p = new clsNhanPhong();
            return $p->kiemTraThoiGianNhanPhong($maDDP);
        }
        
        // Xác nhận nhận phòng - THÊM KIỂM TRA PHÒNG
        public function cXacNhanNhanPhong($maDDP) {
            $p = new clsNhanPhong();
            
            // Kiểm tra đơn có tồn tại và có thể nhận phòng
            if (!$p->kiemTraCoTheNhanPhong($maDDP)) {
                return array(
                    'success' => false,
                    'message' => 'Đơn đặt phòng không tồn tại hoặc không ở trạng thái "Đang chờ".'
                );
            }
            
            // Kiểm tra trạng thái phòng
            $trangThaiPhong = $p->layTrangThaiPhong($maDDP);
            foreach ($trangThaiPhong as $status) {
                if ($status == 'Đang sử dụng') {
                    return array(
                        'success' => false,
                        'message' => 'Phòng đang được sử dụng, không thể nhận phòng.'
                    );
                }
            }
            
            // Kiểm tra thời gian
            if (!$p->kiemTraThoiGianNhanPhong($maDDP)) {
                return array(
                    'success' => false,
                    'message' => 'Không thể nhận phòng do quá hạn 120 phút so với giờ check-in đã đăng ký.'
                );
            }
            
            // Thực hiện nhận phòng
            if ($p->xacNhanNhanPhong($maDDP)) {
                return array(
                    'success' => true,
                    'message' => 'Nhận phòng thành công! Đã cập nhật trạng thái đơn và phòng.'
                );
            } else {
                return array(
                    'success' => false,
                    'message' => 'Lỗi khi nhận phòng. Vui lòng thử lại.'
                );
            }
        }
        
        // Kiểm tra có thể nhận phòng
        public function cKiemTraCoTheNhanPhong($maDDP) {
            $p = new clsNhanPhong();
            return $p->kiemTraCoTheNhanPhong($maDDP);
        }
        
        // Lấy trạng thái phòng
        public function cLayTrangThaiPhong($maDDP) {
            $p = new clsNhanPhong();
            return $p->layTrangThaiPhong($maDDP);
        }
    }
}
?>