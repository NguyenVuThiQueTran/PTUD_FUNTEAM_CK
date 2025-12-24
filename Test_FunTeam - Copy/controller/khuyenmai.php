<?php
header("Content-Type: application/json; charset=utf-8");
require_once dirname(__FILE__) . '/../model/KhuyenMaiModel.php';

$model  = new KhuyenMaiModel();
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {

    /* ===================== DANH SÁCH ===================== */
    case 'list':
        $rs = $model->getAll();
        $data = array();
        while ($row = mysqli_fetch_assoc($rs)) {
            $data[] = $row;
        }
        echo json_encode($data);
        break;

    /* ===================== CHI TIẾT ===================== */
    case 'detail':
        $maKM = isset($_GET['maKM']) ? $_GET['maKM'] : '';
        $rs = $model->getById($maKM);
        if ($row = mysqli_fetch_assoc($rs)) {
            echo json_encode(array("status" => true) + $row);
        } else {
            echo json_encode(array(
                "status" => false,
                "message" => "Không tìm thấy khuyến mãi"
            ));
        }
        break;

    /* ===================== THÊM ===================== */
    case 'create':
        $data = json_decode(file_get_contents("php://input"), true);
        $maKM = trim($data['maKM']);

        // 🔴 CHECK TRÙNG MÃ
        $check = $model->getById($maKM);
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(array(
                "status"  => false,
                "message" => "Mã khuyến mãi đã tồn tại, vui lòng chọn mã khác"
            ));
            exit;
        }

        $model->insert($data);
        echo json_encode(array(
            "status"  => true,
            "message" => "Thêm khuyến mãi thành công"
        ));
        break;

    /* ===================== CẬP NHẬT ===================== */
    case 'update':
        $data = json_decode(file_get_contents("php://input"), true);
        $model->update($data);
        echo json_encode(array(
            "status"  => true,
            "message" => "Cập nhật khuyến mãi thành công"
        ));
        break;

    /* ===================== XÓA ===================== */
  /* ===================== XÓA ===================== */
    case 'delete':
        $maKM = isset($_GET['maKM']) ? $_GET['maKM'] : '';

        if ($model->isUsedInDonDatPhong($maKM)) {
            echo json_encode(array(
                "status"  => false,
                "message" => "Khuyến mãi đang được áp dụng, không thể xóa"
            ));
            exit;
        }

        $model->delete($maKM);
        echo json_encode(array(
            "status"  => true,
            "message" => "Xóa khuyến mãi thành công"
        ));
        break;

        exit;
    



    /* ===================== DEFAULT ===================== */
    default:
        echo json_encode(array(
            "status"  => false,
            "message" => "Action không hợp lệ"
        ));
}
