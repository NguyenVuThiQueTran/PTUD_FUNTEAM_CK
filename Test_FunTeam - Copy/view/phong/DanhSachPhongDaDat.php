<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh sách phòng đã đặt</title>

<style>
body{
    font-family:Arial;
    background:#f5f6fa;
    margin:0;
}
.container{
    max-width:900px;
    margin:40px auto;
    background:#fff;
    border-radius:12px;
    box-shadow:0 8px 30px rgba(0,0,0,.1);
}
.header{
    padding:20px 30px;
    border-bottom:1px solid #eee;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
h2{
    margin:0;
    color:#0b29a4;
}
button{
    background:#0b29a4;
    color:#fff;
    border:none;
    padding:8px 16px;
    border-radius:8px;
    cursor:pointer;
}
button:hover{
    background:#081d6f;
}
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #eee;
}
th{
    background:#0b29a4;
    color:#fff;
}
.btn-danger{
    background:#dc3545;
}
.btn-danger:hover{
    background:#b02a37;
}

/* NÚT HỦY TOÀN BỘ */
.action-left{
    margin:15px 0 20px 30px;
    text-align:left;
}
.btn-huy-all{
    background:#dc3545;
    color:#fff;
    border:none;
    padding:8px 18px;
    border-radius:8px;
    font-weight:bold;
    cursor:pointer;
}
.btn-huy-all:hover{
    background:#b02a37;
}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,.5);
    align-items:center;
    justify-content:center;
}
.modal-box{
    background:#fff;
    padding:20px;
    border-radius:10px;
    width:360px;
    text-align:center;
}
</style>
</head>

<body>

<div class="container">

    <div class="header">
        <h2>Danh sách phòng đã đặt</h2>
        <button onclick="window.location='TraCuuPhong.php'">← Quay lại</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Số phòng</th>
                <th>Ngày nhận</th>
                <th>Ngày trả</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="dsPhong">
            <tr><td colspan="5">Đang tải dữ liệu...</td></tr>
        </tbody>
    </table>

    <!-- NÚT HỦY TOÀN BỘ -->
    <div class="action-left" id="boxHuyAll">
        <button class="btn-huy-all" onclick="huyTatCa()">HỦY TOÀN BỘ</button>
    </div>

</div>

<!-- MODAL -->
<div class="modal" id="modal">
    <div class="modal-box">
        <p id="modalText"></p>
        <br>
        <button onclick="confirmHuy()">Xác nhận</button>
        <button onclick="closeModal()">Hủy</button>
    </div>
</div>

<script>
var huyType = "";
var maDDPSelected = "";

function loadDanhSach(){
    fetch("../../controller/get_phongdadat.php")
    .then(function(r){ return r.json(); })
    .then(function(data){
        var tb = document.getElementById("dsPhong");
        tb.innerHTML = "";

        if(!data || data.length === 0){
            tb.innerHTML =
                "<tr><td colspan='5'>Không có phòng đang đặt</td></tr>";
            document.getElementById("boxHuyAll").style.display = "none";
            return;
        }

        document.getElementById("boxHuyAll").style.display = "block";

        for(var i=0;i<data.length;i++){
            var p = data[i];

            /* CHỈ HIỂN THỊ KHÔNG PHẢI DaHuy */
            if(p.trangThai === "DaHuy") continue;

            var btn = "-";
            if(p.trangThai !== "DaNhan"){
                btn =
                    "<button class='btn-danger' onclick=\"huyMot('"
                    + p.maDDP + "')\">Hủy</button>";
            }

            tb.innerHTML +=
                "<tr>"
                + "<td>" + p.soPhong + "</td>"
                + "<td>" + p.ngayNhanPhong + "</td>"
                + "<td>" + p.ngayTraPhong + "</td>"
                + "<td>" + p.trangThai + "</td>"
                + "<td>" + btn + "</td>"
                + "</tr>";
        }
    });
}

function huyMot(ma){
    huyType = "one";
    maDDPSelected = ma;
    document.getElementById("modalText").innerHTML =
        "Bạn chắc chắn muốn hủy phòng này?";
    openModal();
}

function huyTatCa(){
    huyType = "all";
    document.getElementById("modalText").innerHTML =
        "Bạn chắc chắn muốn hủy <b>TOÀN BỘ</b> phòng đã đặt?";
    openModal();
}

function openModal(){
    document.getElementById("modal").style.display = "flex";
}
function closeModal(){
    document.getElementById("modal").style.display = "none";
}

function confirmHuy(){
    var body = { type: huyType };
    if(huyType === "one"){
        body.maDDP = maDDPSelected;
    }

    fetch("../../controller/huydatphong.php",{
        method:"POST",
        headers:{ "Content-Type":"application/json" },
        body: JSON.stringify(body)
    })
    .then(function(r){ return r.json(); })
    .then(function(res){
        alert(res.message);
        if(res.status){
            loadDanhSach(); // reload -> phòng biến mất
        }
        closeModal();
    });
}

document.addEventListener("DOMContentLoaded", loadDanhSach);
</script>

</body>
</html>
