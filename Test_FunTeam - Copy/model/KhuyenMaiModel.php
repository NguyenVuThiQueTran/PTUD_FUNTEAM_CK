<?php
require_once dirname(__FILE__) . '/../model/clsconnect.php';

class KhuyenMaiModel {
    private $conn;

    public function __construct() {
        $kn = new clsKetNoi();
        $this->conn = $kn->moketnoi();
    }

    public function getAll() {
        $sql = "SELECT maKM, tenCT, mucGiam,
                DATE_FORMAT(ngayBatDau,'%d/%m/%Y') AS ngayBatDau,
                DATE_FORMAT(ngayKetThuc,'%d/%m/%Y') AS ngayKetThuc
                FROM khuyenmai";
        return mysqli_query($this->conn, $sql);
    }

    public function isUsedInDonDatPhong($maKM) {
    $maKM = mysqli_real_escape_string($this->conn, $maKM);
    $sql = "SELECT COUNT(*) AS c FROM dondatphong WHERE maKM='$maKM'";
    $rs = mysqli_query($this->conn, $sql);
    $row = mysqli_fetch_assoc($rs);
    return $row['c'] > 0;
    }

    public function getById($maKM) {
        $maKM = mysqli_real_escape_string($this->conn, $maKM);
        $sql = "SELECT * FROM khuyenmai WHERE maKM='$maKM'";
        return mysqli_query($this->conn, $sql);
    }

    public function insert($d) {
        $sql = "INSERT INTO khuyenmai(maKM, tenCT, mucGiam, ngayBatDau, ngayKetThuc)
                VALUES (
                    '{$d['maKM']}',
                    '{$d['tenCT']}',
                    {$d['mucGiam']},
                    '{$d['ngayBatDau']}',
                    '{$d['ngayKetThuc']}'
                )";
        return mysqli_query($this->conn, $sql);
    }

    public function update($d) {
        $sql = "UPDATE khuyenmai SET
                tenCT='{$d['tenCT']}',
                mucGiam={$d['mucGiam']},
                ngayBatDau='{$d['ngayBatDau']}',
                ngayKetThuc='{$d['ngayKetThuc']}'
                WHERE maKM='{$d['maKM']}'";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($maKM) {
        $maKM = mysqli_real_escape_string($this->conn, $maKM);
        $sql = "DELETE FROM khuyenmai WHERE maKM='$maKM'";
        return mysqli_query($this->conn, $sql);
    }
}
