<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}

$maPhong  = isset($_GET['maPhong']) ? $_GET['maPhong'] : '';
$soPhong  = isset($_GET['soPhong']) ? $_GET['soPhong'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt phòng <?php echo htmlspecialchars($soPhong); ?></title>

<style>
body{
    font-family: Arial;
    background:#f5f6fa;
}
.card{
    max-width:520px;
    margin:50px auto;
    background:#fff;
    padding:30px;
    border-radius:14px;
    box-shadow:0 10px 30px rgba(0,0,0,.15);
    position: relative;
}
h2{
    color:#0b29a4;
    margin-top:0
}
label{
    font-weight:bold;
    margin-top:14px;
    display:block
}
input{
    width:100%;
    padding:10px;
    margin-top:6px;
    border-radius:8px;
    border:1px solid #ccc;
    background:#eef4ff;
    cursor:pointer;
}
.error{
    color:red;
    font-size:13px;
    min-height:16px
}
.total{
    margin-top:15px;
    font-weight:bold;
    color:red
}
.actions{
    margin-top:20px
}
button{
    padding:10px 22px;
    border:none;
    border-radius:8px;
    cursor:pointer
}
.btn-ok{
    background:#0b29a4;
    color:#fff
}
.btn-cancel{
    background:#888;
    color:#fff;
    margin-left:10px
}

/* FIX DATEPICKER */
.datepicker{
    z-index:9999 !important;
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.vi.min.js"></script>
</head>

<body>
<div class="card">
<h2>Đặt phòng <?php echo htmlspecialchars($soPhong); ?></h2>

<label>Họ tên</label>
<input id="hoTen">
<div class="error" id="errHoTen"></div>

<label>Số điện thoại</label>
<input id="sdt" maxlength="10">
<div class="error" id="errSdt"></div>

<label>CCCD</label>
<input id="cccd" maxlength="12">
<div class="error" id="errCccd"></div>

<label>Số người</label>
<input id="soNguoi" type="number" min="1">
<div class="error" id="errSoNguoi"></div>

<label>Ngày nhận phòng</label>
<input id="ngayNhan" readonly>
<div class="error" id="errNhan"></div>

<label>Ngày trả phòng</label>
<input id="ngayTra" readonly>
<div class="error" id="errTra"></div>

<div class="total">
    Tổng tiền: <span id="tongTien">0</span> đ<br>
    Giá phòng: <span id="giaPhongText">0</span> đ / đêm
</div>

<div class="actions">
    <button type="button" class="btn-ok" onclick="xacNhan()">Xác nhận</button>
    <button type="button" class="btn-cancel" onclick="location.href='TraCuuPhong.php'">Hủy</button>
</div>
</div>

<script>
var MA_PHONG = "<?php echo addslashes($maPhong); ?>";
var GIA_PHONG = 0;

/* ===== DATE PICKER FIX CHUẨN ===== */
$('#ngayNhan, #ngayTra').datepicker({
    format: 'dd/mm/yyyy',
    autoclose: true,
    language: 'vi',
    orientation: 'bottom auto',
    container: '.card',
    todayHighlight: true,
    startDate: new Date()
});

/* Luôn bật khi click */
$('#ngayNhan, #ngayTra').on('click', function(){
    $(this).datepicker('show');
});

function toDateVN(s){
    var p=s.split('/');
    return new Date(p[2],p[1]-1,p[0]);
}
function vnToISO(s){
    var p=s.split('/');
    return p[2]+'-'+p[1]+'-'+p[0];
}
function formatVND(n){
    return Number(n||0).toLocaleString('vi-VN');
}

/* Tính tiền */
function tinhTien(){
    var n=ngayNhan.value, t=ngayTra.value;
    if(!n||!t||GIA_PHONG<=0){
        tongTien.innerText='0';
        return;
    }
    var d=(toDateVN(t)-toDateVN(n))/86400000;
    if(d<=0){
        tongTien.innerText='0';
        return;
    }
    tongTien.innerText=formatVND(d*GIA_PHONG);
}
$('#ngayNhan,#ngayTra').change(tinhTien);

/* Load giá phòng */
fetch('../../controller/get_gia_phong.php?maPhong='+MA_PHONG)
.then(function(r){return r.json();})
.then(function(res){
    if(res.status){
        GIA_PHONG=parseInt(res.giaPhong,10);
        giaPhongText.innerText=formatVND(GIA_PHONG);
    }
});

/* Validate */
function setErr(id,msg){document.getElementById(id).innerText=msg||'';}
function clearErr(){
    ['errHoTen','errSdt','errCccd','errSoNguoi','errNhan','errTra']
    .forEach(function(i){setErr(i,'');});
}

function xacNhan(){
    clearErr();
    var hoTenVal=hoTen.value.trim(),
        sdtVal=sdt.value.trim(),
        cccdVal=cccd.value.trim(),
        soNguoiVal=soNguoi.value,
        n=ngayNhan.value,
        t=ngayTra.value;

    var ok=true;

    if(hoTenVal.length<3){setErr('errHoTen','Tên ≥ 3 ký tự');ok=false;}
    if(!/^\d{10}$/.test(sdtVal)){setErr('errSdt','SĐT 10 số');ok=false;}
    if(!/^\d{12}$/.test(cccdVal)){setErr('errCccd','CCCD 12 số');ok=false;}
    if(!soNguoiVal||soNguoiVal<=0){setErr('errSoNguoi','Nhập số người');ok=false;}
    if(!n){setErr('errNhan','Chọn ngày nhận');ok=false;}
    if(!t){setErr('errTra','Chọn ngày trả');ok=false;}
    if(n&&t&&toDateVN(t)<=toDateVN(n)){setErr('errTra','Ngày trả phải sau ngày nhận');ok=false;}

    if(!ok) return;

    fetch("/PTUD_FunTeam-main/controller/datphong.php",{
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify({
            maPhong:MA_PHONG,
            hoTen:hoTenVal,
            sdt:sdtVal,
            cccd:cccdVal,
            soNguoi:soNguoiVal,
            ngayNhan:vnToISO(n),
            ngayTra:vnToISO(t)
        })
    })
    .then(function(r){return r.json();})
    .then(function(res){
        alert(res.message);
        if(res.status) window.location="TraCuuPhong.php";
    })
    .catch(function(){
        alert("Không kết nối được server");
    });
}
</script>
</body>
</html>