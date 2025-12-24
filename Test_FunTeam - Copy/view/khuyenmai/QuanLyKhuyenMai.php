<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Khuyến Mãi </title>
    <link rel="shortcut icon" href="../../img/logos.jpg">

    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">

    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body {
            margin: 0; min-height: 100vh;
            background: linear-gradient(120deg, #e8f0ff, #ffffff);
            display: flex; justify-content: center; align-items: center;
        }
        .management-container {
            width: 100%; max-width: 1000px;
            background: #fff; border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding-bottom: 25px;
        }
        .management-header {
            display: flex; align-items: center;
            padding: 20px 30px; border-bottom: 1px solid #eee;
        }
        .management-header h2 {
            margin: 0; font-size: 26px; font-weight: 700; color: #1f3c88;
            display: flex; align-items: center;
        }
        .management-header i { margin-right: 10px; font-size: 30px; color: #1f3c88; }
        .close-btn { margin-left: auto; font-size: 28px; color: #999; text-decoration: none; }
        .close-btn:hover { color: #e53935; }

        .search-box { padding: 20px 30px 10px; }
        .search-box input {
            width: 100%; padding: 12px 18px;
            border-radius: 30px; border: 1px solid #ccc;
            outline: none; font-size: 14px;
        }

        .table-card {
            margin: 10px 30px; border-radius: 12px; overflow: hidden;
            border: 1px solid #eee;
        }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #1f3c88; color: white; }
        thead th { padding: 12px; font-size: 15px; text-align: center; }
        tbody td { padding: 10px; font-size: 14px; text-align: center; border-bottom: 1px solid #eee; }
        tbody tr:hover { background: #f3f6ff; }

        .action-btns { display: flex; justify-content: center; gap: 8px; }
        .btn-edit {
            background: #f4b400; color: #333;
            padding: 6px 14px; border-radius: 18px; border: none; font-size: 13px;
        }
        .btn-delete {
            background: #e53935; color: white;
            padding: 6px 14px; border-radius: 18px; border: none; font-size: 13px;
        }
        .btn-edit:hover, .btn-delete:hover { opacity: 0.9; }

        .add-wrapper { text-align: center; margin-top: 25px; }
        .btn-add {
            background: #28a745; color: white;
            padding: 12px 30px; border-radius: 30px;
            font-size: 15px; border: none; cursor: pointer;
        }
        .btn-add:hover { opacity: 0.9; }
    </style>
</head>

<body>
<div class="management-container">

    <div class="management-header">
        <h2><i class="bi bi-tags"></i> Quản Lý Khuyến Mãi</h2>
        <a href="../dashboard_quanly.php" class="close-btn">
            <i class="fas fa-times"></i>
        </a>
    </div>

    <div class="search-box">
        <input type="text" id="searchKM"
               placeholder="🔎 Tìm theo Mã KM hoặc Tên chương trình..."
               onkeyup="timKiemKM()">
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Mã KM</th>
                    <th>Tên CT</th>
                    <th>Mức giảm</th>
                    <th>Thời gian hiệu lực</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody id="tbody-khuyenmai">
                <tr><td colspan="5">Đang tải dữ liệu...</td></tr>
            </tbody>
        </table>
    </div>

    <div class="add-wrapper">
        <button class="btn-add" onclick="themKM()">
            <i class="fas fa-plus-circle"></i> Thêm khuyến mãi
        </button>
    </div>

</div>

<script>
var danhSachKM = [];

function loadDanhSachKM() {
    // ✅ sửa: gọi controller mới (không index.php)
    fetch('../../controller/khuyenmai.php?action=list')
        .then(function(res){ return res.json(); })
        .then(function(data){
            danhSachKM = data;
            hienThiBang(data);
        })
        .catch(function(){
            document.getElementById('tbody-khuyenmai').innerHTML =
                '<tr><td colspan="5">Lỗi tải dữ liệu</td></tr>';
        });
}

function hienThiBang(data) {
    var tbody = document.getElementById('tbody-khuyenmai');
    tbody.innerHTML = '';

    if (!data || data.length === 0 || data.status === false) {
        tbody.innerHTML = '<tr><td colspan="5">Không có kết quả</td></tr>';
        return;
    }

    for (var i=0; i<data.length; i++) {
        var row = data[i];
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td>' + row.maKM + '</td>' +
            '<td>' + row.tenCT + '</td>' +
            '<td>' + parseInt(row.mucGiam,10) + '%</td>' +
            '<td>' + row.ngayBatDau + ' - ' + row.ngayKetThuc + '</td>' +
            '<td class="action-btns">' +
                '<button class="btn-edit" onclick="suaKM(\'' + row.maKM + '\')">Sửa</button>' +
                '<button class="btn-delete" onclick="xoaKM(\'' + row.maKM + '\')">Xóa</button>' +
            '</td>';
        tbody.appendChild(tr);
    }
}

function timKiemKM() {
    var tuKhoa = document.getElementById("searchKM").value.toLowerCase();

    var ketQua = danhSachKM.filter(function(km){
        return km.maKM.toLowerCase().indexOf(tuKhoa) !== -1 ||
               km.tenCT.toLowerCase().indexOf(tuKhoa) !== -1;
    });

    hienThiBang(ketQua);
}

function themKM() { window.location.href = 'ThemKhuyenMai.php'; }
function suaKM(maKM) { window.location.href = 'SuaKhuyenMai.php?maKM=' + encodeURIComponent(maKM); }

function xoaKM(maKM) {
    if (!confirm('Bạn có chắc chắn muốn xóa khuyến mãi ' + maKM + '?')) return;

    fetch('../../controller/khuyenmai.php?action=delete&maKM=' + encodeURIComponent(maKM))
        .then(function(res){ return res.json(); })
        .then(function(data){
            alert(data.message || 'Đã xóa');
            loadDanhSachKM();
        })
        .catch(function(){
            alert("Lỗi kết nối server!");
        });
}

loadDanhSachKM();
</script>

</body>
</html>
