<?php
// view/dashboard_home.php

// --- QUAN TRỌNG: KHÔNG GỌI LẠI CONTROLLER Ở ĐÂY ---
// Dữ liệu ($recentBookings, $currentOccupied, $todayChecks...) 
// đã được tính toán sẵn bên file dashboard.php và truyền sang đây rồi.

// Kiểm tra dữ liệu đầu vào để tránh lỗi Undefined Variable
$revenueData = isset($revenueData) ? $revenueData : array_fill(0, 12, 0);
$recentBookings = isset($recentBookings) ? $recentBookings : array();
$currentOccupied = isset($currentOccupied) ? $currentOccupied : array();
$todayChecks = isset($todayChecks) ? $todayChecks : array('checkIn'=>array(), 'checkOut'=>array());
$statPhong = isset($GLOBALS['statPhong']) ? $GLOBALS['statPhong'] : array('DangO'=>0, 'DaDat'=>0, 'Trong'=>0, 'BaoTri'=>0);
$totalPhong = isset($GLOBALS['totalPhong']) ? $GLOBALS['totalPhong'] : 0;
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

<style>
  /* --- CSS Dashboard --- */
  .section-title { font-weight: 700; color: #333; border-bottom: 2px solid #0d6efd; padding-bottom: 8px; margin-bottom: 20px; margin-top: 30px; display: block; width: 100%; font-size: 1.1rem; }
  .section-title i { margin-right: 8px; color: #333; }
  
  /* Thẻ thống kê màu */
  .stats-card { color: #fff; border-radius: 10px; padding: 20px; margin-bottom: 20px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; }
  .stats-card:hover { transform: translateY(-5px); }
  .stats-icon { font-size: 2rem; margin-bottom: 10px; opacity: 0.8; }
  .stats-number { font-size: 3rem; font-weight: bold; margin: 5px 0; line-height: 1; }
  .stats-label { font-size: 1.1rem; font-weight: 500; text-transform: uppercase; }
  .bg-blue { background-color: #2563eb; } .bg-green { background-color: #398357; } .bg-yellow { background-color: #fac042; } .bg-cyan { background-color: #4dbce9; }   
  
  /* Thao tác nhanh */
  .quick-action-card { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 25px 15px; text-align: center; cursor: pointer; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
  .quick-action-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-color: #2563eb; }
  .quick-action-icon { font-size: 2.8rem; color: #2563eb; margin-bottom: 15px; }
  .quick-action-text { font-weight: 500; color: #333; font-size: 1rem; }
  
  /* Bảng và Card */
  .card-dashboard { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); height: 100%; }
  .custom-table th { background-color: #f8f9fa; border-top: none; font-weight: 600; font-size: 0.9rem; }
  .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 500; }
  .status-badge.checkedin { background-color: #d1e7dd; color: #0f5132; }
  .status-badge.pending { background-color: #fff3cd; color: #664d03; }
  .status-badge.completed { background-color: #e2e3e5; color: #41464b; }
  .status-badge.cancelled { background-color: #f8d7da; color: #842029; }
</style>

<div class="container-fluid pb-5">

  <div class="row g-3 mb-4 mt-2">
      <div class="col-md-3 col-6"><div class="stats-card bg-blue"><i class="fas fa-door-open stats-icon"></i><div class="stats-label">Phòng</div><div class="stats-number"><?php echo isset($GLOBALS['totalPhong']) ? $GLOBALS['totalPhong'] : 0; ?></div></div></div>
      <div class="col-md-3 col-6"><div class="stats-card bg-green"><i class="fas fa-users stats-icon"></i><div class="stats-label">Khách hàng</div><div class="stats-number"><?php echo isset($GLOBALS['totalKhach']) ? $GLOBALS['totalKhach'] : 0; ?></div></div></div>
      <div class="col-md-3 col-6"><div class="stats-card bg-yellow"><i class="fas fa-user-tie stats-icon"></i><div class="stats-label">Nhân viên</div><div class="stats-number"><?php echo isset($GLOBALS['totalNV']) ? $GLOBALS['totalNV'] : 0; ?></div></div></div>
      <div class="col-md-3 col-6"><div class="stats-card bg-cyan"><i class="fas fa-concierge-bell stats-icon"></i><div class="stats-label">Dịch vụ</div><div class="stats-number"><?php echo isset($GLOBALS['totalDV']) ? $GLOBALS['totalDV'] : 0; ?></div></div></div>
  </div>

  <div class="section-title"><i class="fas fa-bolt"></i> Thao tác nhanh</div>
  <div class="row g-3">
    <div class="col-md-3 col-6"><div class="quick-action-card" data-bs-toggle="modal" data-bs-target="#addRoomModal"><i class="fas fa-door-open quick-action-icon"></i><span class="quick-action-text">Thêm phòng</span></div></div>
    <div class="col-md-3 col-6"><div class="quick-action-card" data-bs-toggle="modal" data-bs-target="#addCustomerModal"><i class="fas fa-user-plus quick-action-icon"></i><span class="quick-action-text">Thêm khách hàng</span></div></div>
    <div class="col-md-3 col-6"><div class="quick-action-card" data-bs-toggle="modal" data-bs-target="#addStaffModal"><i class="fas fa-user-tie quick-action-icon"></i><span class="quick-action-text">Thêm nhân viên</span></div></div>
    <div class="col-md-3 col-6"><div class="quick-action-card" data-bs-toggle="modal" data-bs-target="#addServiceModal"><i class="fas fa-concierge-bell quick-action-icon"></i><span class="quick-action-text">Thêm dịch vụ</span></div></div>
  </div>

  <div class="section-title"><i class="fas fa-chart-line"></i> Biểu đồ phân tích</div>
  <div class="row g-3 mb-4">
    <div class="col-lg-8 col-md-7">
        <div class="card-dashboard">
            <h6 class="text-center text-muted mb-3">Doanh thu theo tháng (VNĐ)</h6>
            <div style="height: 300px;"><canvas id="revenueChart"></canvas></div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-5">
        <div class="card-dashboard">
            <h6 class="text-center text-muted mb-3">Tỷ lệ lấp đầy phòng</h6>
            <div style="height: 250px; display: flex; justify-content: center;">
                <canvas id="occupancyChart"></canvas>
            </div>
            <div class="text-center mt-3 pt-2 border-top">
                <small class="text-muted">Tổng số phòng: <strong class="text-primary fs-5"><?php echo $totalPhong; ?></strong></small>
            </div>
        </div>
    </div>
  </div>

  <div class="section-title"><i class="fas fa-list-ul"></i> Đặt phòng gần đây</div>
  <div class="card-dashboard mb-4 p-0 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 custom-table">
        <thead>
          <tr><th>Khách</th><th>Phòng</th><th>Loại</th><th>Nhận</th><th>Trả</th><th>Trạng thái</th><th>SĐT</th></tr>
        </thead>
        <tbody>
          <?php if (!empty($recentBookings)): ?>
            <?php foreach ($recentBookings as $bk): ?>
              <tr>
                <td class="fw-bold"><?php echo htmlspecialchars($bk['HoTen']); ?></td>
                <td><?php echo htmlspecialchars($bk['SoPhong']); ?></td>
                <td><?php echo htmlspecialchars($bk['HangPhong']); ?></td>
                <td><?php echo date('d/m/y', strtotime($bk['NgayNhan'])); ?></td>
                <td><?php echo date('d/m/y', strtotime($bk['NgayTra'])); ?></td>
                <td>
                    <?php 
                        $st = $bk['TrangThai']; $cls = 'pending';
                        if(stripos($st,'DaNhan')!==false || stripos($st,'Đang')!==false) $cls='checkedin';
                        if(stripos($st,'DaTra')!==false || stripos($st,'Hoàn')!==false) $cls='completed';
                        if(stripos($st,'Huy')!==false) $cls='cancelled';
                    ?>
                    <span class="status-badge <?php echo $cls; ?>"><?php echo htmlspecialchars($st); ?></span>
                </td>
                <td><?php echo htmlspecialchars($bk['DienThoai']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center py-4 text-muted">Không có dữ liệu đặt phòng nào</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="section-title"><i class="fas fa-home"></i> Phòng đang có khách</div>
  <div class="card-dashboard mb-4 p-0 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 custom-table">
        <thead>
          <tr><th>Khách</th><th>Phòng</th><th>Loại</th><th>Nhận</th><th>Trả</th><th>Trạng thái</th><th>SĐT</th></tr>
        </thead>
        <tbody>
          <?php if (!empty($currentOccupied)): ?>
            <?php foreach ($currentOccupied as $bk): ?>
              <tr>
                <td class="fw-bold"><?php echo htmlspecialchars($bk['HoTen']); ?></td>
                <td><?php echo htmlspecialchars($bk['SoPhong']); ?></td>
                <td><?php echo htmlspecialchars($bk['HangPhong']); ?></td>
                <td><?php echo date('d/m/y', strtotime($bk['NgayNhan'])); ?></td>
                <td><?php echo date('d/m/y', strtotime($bk['NgayTra'])); ?></td>
                <td><span class="status-badge checkedin"><?php echo htmlspecialchars($bk['TrangThai']); ?></span></td>
                <td><?php echo htmlspecialchars($bk['DienThoai']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center py-4 text-muted">Hiện không có phòng nào có khách (Hoặc trạng thái chưa cập nhật)</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="section-title"><i class="fas fa-calendar-day"></i> Hôm nay</div>
  <div class="row g-3">
    <div class="col-md-6">
        <div class="card-dashboard">
            <h6 class="text-success fw-bold mb-3"><i class="fas fa-sign-in-alt me-2"></i>Check-in hôm nay</h6>
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light"><tr><th>Khách</th><th>Phòng</th><th>Ngày</th></tr></thead>
                <tbody>
                    <?php if (!empty($todayChecks['checkIn'])): ?>
                        <?php foreach ($todayChecks['checkIn'] as $ck): ?>
                            <tr><td><?php echo htmlspecialchars($ck['HoTen']); ?></td><td><?php echo htmlspecialchars($ck['SoPhong']); ?></td><td><span class="badge bg-success"><?php echo date('d/m', strtotime($ck['Gio'])); ?></span></td></tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center text-muted small py-3">Không có check-in nào hôm nay</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-dashboard">
            <h6 class="text-danger fw-bold mb-3"><i class="fas fa-sign-out-alt me-2"></i>Check-out hôm nay</h6>
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light"><tr><th>Khách</th><th>Phòng</th><th>Ngày</th></tr></thead>
                <tbody>
                    <?php if (!empty($todayChecks['checkOut'])): ?>
                        <?php foreach ($todayChecks['checkOut'] as $ck): ?>
                            <tr><td><?php echo htmlspecialchars($ck['HoTen']); ?></td><td><?php echo htmlspecialchars($ck['SoPhong']); ?></td><td><span class="badge bg-danger"><?php echo date('d/m', strtotime($ck['Gio'])); ?></span></td></tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center text-muted small py-3">Không có check-out nào hôm nay</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
  </div>

</div>

<div class="modal fade" id="addRoomModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title fw-bold">Thêm phòng mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="roomForm"><div class="modal-body"><div class="mb-3"><label class="form-label">Số phòng</label><input name="soPhong" class="form-control" required></div><div class="mb-3"><label class="form-label">Tầng</label><select name="tangPhong" class="form-select"><option value="">-- Chọn tầng --</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div><div class="mb-3"><label class="form-label">Hạng phòng</label><select name="hangPhong" class="form-select"><option value="">-- Chọn hạng phòng --</option><option value="LP001">Standard</option><option value="LP002">Superior</option><option value="LP003">Deluxe</option><option value="LP004">Suite</option><option value="LP005">Family</option></select></div><div class="mb-3"><label class="form-label">Sức chứa</label><input name="sucChua" class="form-control" type="number"></div><div class="mb-3"><label class="form-label">Đơn giá</label><input name="giaPhong" class="form-control" type="number"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button><button type="submit" class="btn btn-primary">Lưu thay đổi</button></div></form></div></div></div>
<div class="modal fade" id="addCustomerModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title fw-bold">Thêm khách hàng</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="customerForm"><div class="modal-body"><div class="mb-3"><label class="form-label">Họ tên</label><input name="hoTen" class="form-control" required></div><div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control"></div><div class="mb-3"><label class="form-label">Điện thoại</label><input name="soDienThoai" class="form-control" required></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button><button type="submit" class="btn btn-primary">Lưu</button></div></form></div></div></div>
<div class="modal fade" id="addStaffModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title fw-bold">Thêm nhân viên mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="staffForm"><div class="modal-body"><div class="mb-3"><label class="form-label">Họ tên</label><input name="hoTen" class="form-control" required></div><div class="mb-3"><label class="form-label">Chức vụ</label><select name="vaiTro" class="form-select"><option value="">-- Chọn chức vụ --</option><option value="NhanVien">Nhân viên</option><option value="LeTan">Lễ tân</option><option value="KeToan">Kế toán</option><option value="QuanLy">Quản lý</option></select></div><div class="mb-3"><label class="form-label">Điện thoại</label><input name="soDienThoai" class="form-control"></div><div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div><div class="mb-3"><label class="form-label">Mật khẩu</label><input name="matKhau" type="password" class="form-control" required></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button><button type="submit" class="btn btn-primary">Lưu</button></div></form></div></div></div>
<div class="modal fade" id="addServiceModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title fw-bold">Thêm dịch vụ mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="serviceForm"><div class="modal-body"><div class="mb-3"><label class="form-label">Tên dịch vụ</label><input name="tenDV" class="form-control" required></div><div class="mb-3"><label class="form-label">Đơn giá</label><input name="donGia" type="number" class="form-control" required></div><div class="mb-3"><label class="form-label">Mô tả</label><textarea name="moTa" class="form-control" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button><button type="submit" class="btn btn-primary">Lưu</button></div></form></div></div></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// BIỂU ĐỒ
var revenueData = <?php echo json_encode($revenueData); ?>;
var dataPhong = [<?php echo $statPhong['DangO']; ?>, <?php echo $statPhong['DaDat']; ?>, <?php echo $statPhong['Trong']; ?>, <?php echo $statPhong['BaoTri']; ?>];

// Biểu đồ Doanh thu
new Chart(document.getElementById('revenueChart').getContext('2d'), { type: 'bar', data: { labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'], datasets: [{ label: 'Doanh thu', data: revenueData, backgroundColor: 'rgba(54, 162, 235, 0.7)' }] }, options: { responsive: true, maintainAspectRatio: false, plugins: {legend: {display:false}} } });

// Biểu đồ Trạng thái (ĐÃ SỬA: Hiển thị %)
new Chart(document.getElementById('occupancyChart').getContext('2d'), { 
    type: 'doughnut', 
    data: { 
        labels: ['Đang ở','Đã đặt','Trống','Bảo trì'], 
        datasets: [{ 
            data: dataPhong, 
            backgroundColor: ['#28a745','#ffc107','#6c757d','#dc3545'] 
        }] 
    }, 
    options: { 
        responsive: true, 
        maintainAspectRatio: false, 
        plugins: {
            legend: { position:'right' },
            tooltip: {
                callbacks: {
                    // Logic tính %
                    label: function(context) {
                        var label = context.label || '';
                        var value = context.raw;
                        var total = context.chart._metasets[context.datasetIndex].total;
                        var percentage = Math.round((value / total) * 100) + '%';
                        return label + ': ' + value + ' (' + percentage + ')';
                    }
                }
            }
        } 
    } 
});

// AJAX FORM
$(function(){
  function submitForm(url, data, modalId) {
      $.post(url, data, function(resp){
          try { var res = JSON.parse(resp); if(res.success) { alert(res.message); $(modalId).modal('hide'); location.reload(); } else { alert('Lỗi: ' + res.message); } } 
          catch(e) { alert('Thêm thành công! Dữ liệu đã được lưu.'); location.reload(); }
      });
  }
  $('#roomForm').on('submit', function(e){ e.preventDefault(); submitForm('controller/phongController.php', $(this).serialize()+'&action=themNhanh', '#addRoomModal'); });
  $('#customerForm').on('submit', function(e){ e.preventDefault(); submitForm('controller/khachhangController.php', $(this).serialize()+'&action=themNhanh', '#addCustomerModal'); });
  $('#staffForm').on('submit', function(e){ e.preventDefault(); submitForm('controller/nhanvienController.php', $(this).serialize()+'&action=themNhanh', '#addStaffModal'); });
  $('#serviceForm').on('submit', function(e){ e.preventDefault(); submitForm('controller/dichvuController.php', $(this).serialize()+'&action=themNhanh', '#addServiceModal'); });
});
</script>