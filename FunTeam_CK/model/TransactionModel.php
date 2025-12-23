<?php
require_once __DIR__ . '/clsconnect.php';

class TransactionModel extends clsKetNoi
{
    // ============================
    // 1. TÌM KIẾM GIAO DỊCH
    // ============================
    public function searchTransaction($keyword)
    {
        $conn = $this->moketnoi();

        $keyword = $conn->real_escape_string($keyword);

        $sql = "
            SELECT maGD, maKH, maDoan, soDienThoai, CCCD, ngayBatDau, ngayKetThuc, trangThai
            FROM giaodich
            WHERE maKH LIKE '%$keyword%'
               OR maDoan LIKE '%$keyword%'
               OR soDienThoai LIKE '%$keyword%'
               OR CCCD LIKE '%$keyword%'
        ";

        $result = $conn->query($sql);

        $this->dongketnoi();
        return $result;
    }

    // ============================
    // 2. LẤY CHI TIẾT GIAO DỊCH
    // ============================
    public function getTransactionById($maGD)
    {
        $conn = $this->moketnoi();

        $maGD = $conn->real_escape_string($maGD);

        $sql = "SELECT * FROM giaodich WHERE maGD = '$maGD' LIMIT 1";

        $result = $conn->query($sql);
        $data = $result->fetch_assoc();

        $this->dongketnoi();
        return $data;
    }

    // ============================
    // 3. HỦY GIAO DỊCH
    // ============================
    public function cancelTransaction($maGD)
    {
        $conn = $this->moketnoi();
        $maGD = $conn->real_escape_string($maGD);

        // Lấy giờ hiện tại
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $now = date("Y-m-d H:i:s");

        // Kiểm tra thời gian giao dịch
        $sql = "SELECT ngayBatDau FROM giaodich WHERE maGD = '$maGD' LIMIT 1";
        $check = $conn->query($sql)->fetch_assoc();

        if (!$check) {
            return array("success" => false, "msg" => "Không tìm thấy giao dịch.");
        }

        $timeStart = $check['ngayBatDau'];

        // Đã quá hạn hủy
        if ($now > $timeStart) {
            return array(
                "success" => false,
                "msg" => "Không thể hủy giao dịch do đã qua thời hạn được phép hủy."
            );
        }

        // Cập nhật trạng thái
        $update = "
            UPDATE giaodich 
            SET trangThai = ''
            WHERE maGD = '$maGD'
        ";

        if ($conn->query($update)) {
            $this->dongketnoi();
            return array("success" => true, "msg" => " giao dịch thành công.");
        }

        $this->dongketnoi();
        return array("success" => false, "msg" => "Lỗi khi hủy giao dịch.");
    }
}
?>
