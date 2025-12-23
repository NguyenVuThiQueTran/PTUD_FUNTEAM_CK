<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Xem báo cáo</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{font-family:Arial;background:#f4f6f9}
.wrap{max-width:1100px;margin:40px auto;background:#fff;padding:30px;border-radius:12px}
.top{display:flex;justify-content:space-between;align-items:center}
select,button{padding:6px 10px}
.row{display:flex;gap:20px;margin-top:20px}
.box{flex:1;border:1px solid #eee;border-radius:10px;padding:15px}
.summary p{font-size:18px}
.wrap{
    position:relative;
}
.close-btn{
    position:absolute;
    top:15px;
    right:20px;
    font-size:20px;
    font-weight:bold;
    cursor:pointer;
    color:#555;
    padding:4px 10px;
    border-radius:50%;
    transition:0.2s;
}
.close-btn:hover{
    background:#e74a3b;
    color:#fff;
}

</style>
</head>

<body>
<div class="wrap">
    <div class="close-btn" onclick="goBack()">✕</div>

    <div class="top">
        <h2>📊 Thống kê báo cáo</h2>
        <div>
            Năm 1:
            <select id="nam1"></select>
            Năm 2:
            <select id="nam2"></select>
            <button onclick="loadBaoCao()">Xem</button>
            <button onclick="xuatExcel()">Xuất Excel</button>
        </div>
    </div>

    <div class="row">
        <div class="box">
            <canvas id="chart"></canvas>
        </div>
        <div class="box summary">
            <h3>Tổng hợp</h3>
            <p>👤 Khách hàng: <b id="khachHang">0</b></p>
            <p>🛎 Dịch vụ: <b id="dichVu">0</b></p>
        </div>
    </div>
</div>

<script>
const years = [];
const now = new Date().getFullYear();
for(let i=now-5;i<=now;i++) years.push(i);

years.forEach(y=>{
    nam1.innerHTML += `<option value="${y}">${y}</option>`;
    nam2.innerHTML += `<option value="${y}">${y}</option>`;
});
nam1.value = now;
nam2.value = now-1;

let chart;

function loadBaoCao(){
    fetch(`../../controller/baocao.php?nam1=${nam1.value}&nam2=${nam2.value}`)
    .then(r=>r.json())
    .then(d=>{
        document.getElementById("khachHang").innerText = d.khachHang;
        document.getElementById("dichVu").innerText = d.dichVu;

        if(chart) chart.destroy();
        chart = new Chart(document.getElementById("chart"),{
            type:'bar',
            data:{
                labels:[1,2,3,4,5,6,7,8,9,10,11,12],
                datasets:[
                    {
                        label:'Năm '+d.nam1,
                        backgroundColor:'#4e73df',
                        data:d.doanhThuNam1
                    },
                    {
                        label:'Năm '+d.nam2,
                        backgroundColor:'#e74a3b',
                        data:d.doanhThuNam2
                    }
                ]
            }
        });
    });
}

function xuatExcel(){
    window.location =
    `../../controller/baocao.php?export=excel&nam=${nam1.value}`;
}

loadBaoCao();

function goBack(){
    window.history.back();
}

</script>
</body>
</html>
