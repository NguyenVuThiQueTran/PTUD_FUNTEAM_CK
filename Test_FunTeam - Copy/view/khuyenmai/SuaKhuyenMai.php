<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa khuyến mãi</title>

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

<h2>Sửa khuyến mãi / gói dịch vụ</h2>

<form id="formKM">
  <label>Mã khuyến mãi:</label>
  <input type="text" id="maKM" readonly>

  <label>Tên chương trình:</label>
  <input type="text" id="tenCT">

  <label>Mức giảm (%):</label>
  <input type="number" id="mucGiam" min="0" max="100">

  <label>Ngày bắt đầu:</label>
  <input type="text" id="ngayBatDau" placeholder="dd/mm/yyyy">

  <label>Ngày kết thúc:</label>
  <input type="text" id="ngayKetThuc" placeholder="dd/mm/yyyy">

  <button type="button" class="btn-save" onclick="capNhatKM()">Cập nhật</button>
  <button type="button" class="btn-cancel" onclick="huy()">Hủy</button>
</form>

<script>
$(function() {
  $('#ngayBatDau, #ngayKetThuc').datepicker({
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayHighlight: true,
    language: 'vi'
  });

  loadThongTin();
});

function getMaKM() {
  var params = new URLSearchParams(window.location.search);
  return params.get("maKM");
}

function doiNgay(ngayISO) {
  var parts = ngayISO.split("-");
  return parts[2] + "/" + parts[1] + "/" + parts[0];
}

function chuyenVeISO(ngayVN) {
  var parts = ngayVN.split("/");
  return parts[2] + "-" + parts[1] + "-" + parts[0];
}

function loadThongTin() {
  var maKM = getMaKM();
  if (!maKM) {
    alert("❌ Thiếu mã khuyến mãi!");
    return;
  }

  // ✅ sửa: gọi controller mới
  fetch("../../controller/khuyenmai.php?action=detail&maKM=" + encodeURIComponent(maKM))
    .then(function(res){ return res.json(); })
    .then(function(data){
      if (data.status === false) {
        alert("❌ Không tìm thấy khuyến mãi!");
        return;
      }
      document.getElementById("maKM").value = data.maKM;
      document.getElementById("tenCT").value = data.tenCT;
      document.getElementById("mucGiam").value = parseInt(data.mucGiam,10);
      document.getElementById("ngayBatDau").value = doiNgay(data.ngayBatDau);
      document.getElementById("ngayKetThuc").value = doiNgay(data.ngayKetThuc);
    })
    .catch(function(err){
      alert("❌ Lỗi tải dữ liệu!");
      console.log(err);
    });
}

function capNhatKM() {
  var maKM = document.getElementById("maKM").value.trim();
  var tenCT = document.getElementById("tenCT").value.trim();
  var mucGiam = document.getElementById("mucGiam").value.trim();
  var bd = document.getElementById("ngayBatDau").value.trim();
  var kt = document.getElementById("ngayKetThuc").value.trim();

  if (!tenCT || !mucGiam || !bd || !kt) {
    alert("⚠️ Vui lòng nhập đầy đủ thông tin!");
    return;
  }
  if (mucGiam < 0 || mucGiam > 100) {
    alert("⚠️ Mức giảm không hợp lệ!");
    return;
  }

  var bdISO = chuyenVeISO(bd);
  var ktISO = chuyenVeISO(kt);

  if (new Date(ktISO) < new Date(bdISO)) {
    alert("⚠️ Ngày kết thúc không hợp lệ!");
    return;
  }

  var data = {
    maKM: maKM,
    tenCT: tenCT,
    mucGiam: mucGiam,
    ngayBatDau: bdISO,
    ngayKetThuc: ktISO
  };

  // ✅ sửa: gọi controller mới
  fetch("../../controller/khuyenmai.php?action=update", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
  .then(function(res){ return res.json(); })
  .then(function(resp){
    alert(resp.message || "Cập nhật thành công!");
    window.location.href = "QuanLyKhuyenMai.php";
  })
  .catch(function(err){
    alert("❌ Lỗi khi cập nhật: " + err);
  });
}

function huy() {
  window.location.href = "QuanLyKhuyenMai.php";
}
</script>

</body>
</html>
