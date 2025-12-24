<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm khuyến mãi</title>

<style>
body { font-family: Arial, sans-serif; margin: 30px; background: #fff; }
form { max-width: 500px; margin: auto; border: 1px solid #ccc; padding: 20px; border-radius: 6px; }
label { display: block; margin-top: 10px; font-weight: bold; }
input { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px; }
button { margin-top: 15px; padding: 8px 16px; border: none; border-radius: 4px; color: #fff; cursor: pointer; }
.btn-save { background: #28a745; }
.btn-cancel { background: #6c757d; margin-left: 10px; }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.vi.min.js"></script>
</head>
<body>

<h2>Thêm khuyến mãi / gói dịch vụ</h2>

<form id="formKM">
  <label>Mã khuyến mãi:</label>
  <input type="text" id="maKM" name="maKM" placeholder="VD: KM001">

  <label>Tên chương trình:</label>
  <input type="text" id="tenCT" name="tenCT" placeholder="VD: Giảm giá hè">

  <label>Mức giảm (%):</label>
  <input type="number" id="mucGiam" name="mucGiam" min="0" max="100">

  <label>Ngày bắt đầu:</label>
  <input type="text" id="ngayBatDau" name="ngayBatDau" placeholder="dd/mm/yyyy">

  <label>Ngày kết thúc:</label>
  <input type="text" id="ngayKetThuc" name="ngayKetThuc" placeholder="dd/mm/yyyy">

  <button type="button" class="btn-save" onclick="luuKM()">Lưu</button>
  <button type="button" class="btn-cancel" onclick="huy()">Hủy</button>
</form>

<script>
$(function() {
  $('#ngayBatDau, #ngayKetThuc').datepicker({
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayHighlight: true,
    language: 'vi',
    todayHighlight: true,
    startDate: new Date()
  });
});

function chuyenVeISO(ngayVN) {
  var parts = ngayVN.split('/');
  return parts[2] + '-' + parts[1] + '-' + parts[0];
}

function luuKM() {
  var maKM = $('#maKM').val().trim();
  var tenCT = $('#tenCT').val().trim();
  var mucGiam = $('#mucGiam').val().trim();
  var bd = $('#ngayBatDau').val().trim();
  var kt = $('#ngayKetThuc').val().trim();

  if (!maKM || !tenCT || !mucGiam || !bd || !kt) {
    alert("⚠️ Vui lòng nhập đầy đủ thông tin!");
    return;
  }

  if (mucGiam < 0 || mucGiam > 100) {
    alert("⚠️ Mức giảm giá không hợp lệ (0–100%)!");
    return;
  }

  var bdISO = chuyenVeISO(bd);
  var ktISO = chuyenVeISO(kt);

  if (new Date(ktISO) < new Date(bdISO)) {
    alert("⚠️ Ngày kết thúc không được nhỏ hơn ngày bắt đầu!");
    return;
  }

  var data = {
    maKM: maKM,
    tenCT: tenCT,
    mucGiam: mucGiam,
    ngayBatDau: bdISO,
    ngayKetThuc: ktISO
  };

  fetch('../../controller/khuyenmai.php?action=create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  })
  .then(function(res){ return res.json(); })
  .then(function(resp){
    alert(resp.message);
    if (resp.status === true) {
      window.location.href = "QuanLyKhuyenMai.php";
    }
  })
  .catch(function(err){
    alert("❌ Lỗi khi gửi dữ liệu: " + err);
  });
}

function huy() {
  window.location.href = "QuanLyKhuyenMai.php";
}
</script>
</body>
</html>
