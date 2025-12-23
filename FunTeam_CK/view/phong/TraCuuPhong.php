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
<title>Tra cứu phòng - TravelX</title>

<style>
body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin:0; }
.container { max-width: 1100px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.1); border: 1px solid #e0e0e0; }
.header { padding: 20px 30px; border-bottom: 1px solid #e0e0e0; }
.header h2 { margin:0; font-size:24px; color:#0b29a4; }

.filter-box { padding:25px 30px; display:grid; grid-template-columns: repeat(3, 1fr); gap:20px; }
.filter-box label { font-weight:bold; font-size:14px; }
.filter-box select, .filter-box input { width:100%; padding:8px; margin-top:6px; border-radius:6px; border:1px solid #ccc; }

.price-range { grid-column: 1 / span 3; }
.price-value { text-align:center; font-weight:bold; margin-top:6px; color:#0b29a4; }

button { background-color:#0b29a4; color:white; border:none; padding:10px 24px; border-radius:8px; cursor:pointer; }
button:hover { background-color:#081d6f; }

.table-box { padding:0 30px 30px; }
table { width:100%; border-collapse: collapse; }
thead th { background-color:#0b29a4; color:white; padding:12px; }
tbody td { padding:12px; text-align:center; border-bottom:1px solid #eee; }
tbody tr:hover { background-color:#eef4ff; }

.trong { color: green; font-weight: bold; }
.dadat { color: red; font-weight: bold; }
.dango { color: orange; font-weight: bold; }
.baotri { color: gray; font-weight: bold; }

.header-bar {
    display: flex;
    align-items: center;
    padding: 20px 30px;
    border-bottom: 1px solid #e0e0e0;
}

/* ĐẨY BUTTON SANG PHẢI */
.btn-my-booking {
    margin-left: auto;   /* 👈 DÒNG QUAN TRỌNG */
    
    background-color: #fff;
    color: #0b29a4;
    border: 2px solid #0b29a4;
    padding: 6px 14px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    white-space: nowrap;
}

.btn-my-booking:hover {
    background-color: #0b29a4;
    color: #fff;
}

.header-bar h2 {
    margin: 0;
    font-size: 24px;
    color: #0b29a4;
}

.close-btn{
    margin-left: 18px;          /* cách nút bên cạnh */
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    color: #0b29a4;
    padding: 4px 10px;
    border-radius: 50%;
    border: 2px solid #0ba43bff;
    background: #eef3f0ff;
    transition: 0.2s;
    flex-shrink: 0;
}

.close-btn:hover{
    background: #e74a3b;
    color: #f2ededff;
    border-color: #e74a3b;
}




</style>
</head>

<body>

<div class="header-bar">
    <h2>Tra cứu phòng, tình trạng, giá</h2>

    <button class="btn-my-booking" onclick="xemPhongDaDat()">
        📋 Xem danh sách phòng đã đặt
    </button>

    <div class="close-btn" onclick="goBackKhachHang()">✕</div>
</div>








<div class="filter-box">
    <div>
        <label>Loại phòng</label>
        <select id="loaiPhong">
            <option value="">-- Tất cả --</option>
            <option value="LP001">LP001</option>
            <option value="LP002">LP002</option>
            <option value="LP003">LP003</option>
            <option value="LP004">LP004</option>
            <option value="LP005">LP005</option>
           
        </select>
    </div>

    <div>
        <label>Trạng thái</label>
        <select id="tinhTrang">
            <option value="">-- Tất cả --</option>
            <option value="Trống">Trống</option>
            <option value="Đã đặt">Đã đặt</option>
            <option value="Bảo trì">Bảo trì</option>
            <option value="Đang ở">Đang ở</option>
        </select>
    </div>

    <div>
        <label>Hạng phòng</label>
        <select id="hangPhong">
            <option value="">-- Tất cả --</option>
            <option value="Standard">Standard</option>
            <option value="Superior">Superior</option>
            <option value="Deluxe">Deluxe</option>
            <option value="Suite">Suite</option>
            <option value="Family">Family</option>
        </select>
    </div>

    <div>
        <label>Số giường</label>
        <select id="soGiuong">
            <option value="">-- Tất cả --</option>
            <option value="1">1 giường đơn</option>
            <option value="2">1 giường đôi</option>
            <option value="3">2 giường đơn</option>
            <option value="4">2 giường đôi</option>
        </select>
    </div>

    <div class="price-range">
        <label>Khoảng giá (VNĐ)</label>
        <input type="range" id="giaMin" min="100000" max="50000000" step="50000" value="100000">
        <input type="range" id="giaMax" min="100000" max="50000000" step="50000" value="50000000">
        <div class="price-value">
            <span id="giaMinText">100.000</span> đ -
            <span id="giaMaxText">50.000.000</span> đ
        </div>
    </div>

    <div>
        <button onclick="timPhong()">Tìm kiếm</button>
    </div>
</div>

<div class="table-box">
<table>
<thead>
<tr>
    <th>Loại phòng</th>
    <th>Hạng phòng</th>
    <th>Số phòng</th>
    <th>Trạng thái</th>
    <th>Giá phòng</th>
    <th>Số giường</th>
</tr>
</thead>
<tbody id="ketqua">
<tr><td colspan="6">Đang tải dữ liệu...</td></tr>
</tbody>
</table>
</div>

</div>

<script>
/* ===== TIỆN ÍCH ===== */
function formatVND(n){
    return Number(n).toLocaleString("vi-VN");
}
function doiSoGiuong(n){
    if(n==1) return "1 giường đơn";
    if(n==2) return "1 giường đôi";
    if(n==3) return "2 giường đơn";
    if(n==4) return "2 giường đôi";
    return n;
}

/* ===== SLIDER GIÁ ===== */
const giaMin = document.getElementById("giaMin");
const giaMax = document.getElementById("giaMax");
const giaMinText = document.getElementById("giaMinText");
const giaMaxText = document.getElementById("giaMaxText");

function capNhatGia(){
    let min = parseInt(giaMin.value,10);
    let max = parseInt(giaMax.value,10);
    if(min > max){
        max = min;
        giaMax.value = max;
    }
    giaMinText.innerText = formatVND(min);
    giaMaxText.innerText = formatVND(max);
}
giaMin.oninput = capNhatGia;
giaMax.oninput = capNhatGia;
capNhatGia();

/* ===== LOAD PHÒNG ===== */
document.addEventListener("DOMContentLoaded", function(){
    timPhong();
});

function timPhong(){
    ketqua.innerHTML = "<tr><td colspan='6'>Đang tải dữ liệu...</td></tr>";

    // URL đúng
    const url = "../../controller/phong.php" +
        "?loaiPhong=" + encodeURIComponent(loaiPhong.value) +
        "&hangPhong=" + encodeURIComponent(hangPhong.value) +
        "&soGiuong=" + encodeURIComponent(soGiuong.value) +
        "&tinhTrang=" + encodeURIComponent(tinhTrang.value) +
        "&giaMin=" + giaMin.value +
        "&giaMax=" + giaMax.value;
    
    console.log("Fetch URL:", url);
    
    fetch(url)
    .then(res => {
        console.log("Status:", res.status);
        if (!res.ok) {
            throw new Error('HTTP ' + res.status);
        }
        return res.json();
    })
    .then(data => {
        console.log("Data received:", data);
        
        if (!data || data.length === 0) {
            ketqua.innerHTML = "<tr><td colspan='6'>Không có phòng phù hợp</td></tr>";
            return;
        }
        
        // Xử lý hiển thị
        let html = '';
        data.forEach(p => {
            // Giải mã Unicode escape sequences
            let trangThai = decodeUnicode(p.tinhTrang);
            let cls = "trong";
            let click = "";
            
            // So sánh với chuỗi đã giải mã
            if (trangThai === "Đã đặt") cls = "dadat";
            else if (trangThai === "Đang ở") cls = "dango";
            else if (trangThai === "Bảo trì") cls = "baotri";
            
            if (trangThai === "Trống") {
                click = `onclick="datPhong('${p.maPhong}','${p.soPhong}')"`;
            }
            
            // Giải mã các field khác nếu cần
            let loaiPhongDisplay = decodeUnicode(p.maLoaiPhong);
            let hangPhongDisplay = decodeUnicode(p.hangPhong);
            
            html += `
                <tr ${click} style="cursor:${click ? 'pointer':'default'}">
                    <td>${loaiPhongDisplay}</td>
                    <td>${hangPhongDisplay}</td>
                    <td>${p.soPhong}</td>
                    <td class="${cls}">${trangThai}</td>
                    <td>${formatVND(p.giaPhong)} đ</td>
                    <td>${doiSoGiuong(p.sucChua)}</td>
                </tr>
            `;
        });
        
        ketqua.innerHTML = html;
    })
    .catch(err => {
        console.error("Error:", err);
        ketqua.innerHTML = `<tr><td colspan='6'>Lỗi: ${err.message}</td></tr>`;
    });
}

// Hàm giải mã Unicode escape sequences
function decodeUnicode(str) {
    if (typeof str !== 'string') return str;
    
    // Nếu có escape sequences \u
    if (str.includes('\\u')) {
        try {
            return str.replace(/\\u[\dA-F]{4}/gi, 
                function(match) {
                    return String.fromCharCode(parseInt(match.replace(/\\u/g, ''), 16));
                }
            );
        } catch(e) {
            console.warn("Decode unicode error:", e);
            return str;
        }
    }
    return str;
}

/* ===== ĐẶT PHÒNG ===== */
function datPhong(maPhong, soPhong){
    window.location =
        "DatPhong.php?maPhong=" + maPhong + "&soPhong=" + soPhong;
}

function xemPhongDaDat(){
    window.location.href = "DanhSachPhongDaDat.php";
}


document.addEventListener("DOMContentLoaded", function () {
    // XÓA backdrop nếu còn sót
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

    // Khôi phục scroll + click
    document.body.classList.remove('modal-open');
    document.body.style.overflow = 'auto';
});

function goBackKhachHang(){
    window.location.href = '../dashboard_khachhang.php';
}


</script>

</body>
</html>
