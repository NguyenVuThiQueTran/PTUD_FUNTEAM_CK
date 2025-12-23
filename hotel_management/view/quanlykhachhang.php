<div class="d-flex justify-content-between mb-3">
  <h1>Quản lý Khách hàng</h1>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#khachHangModal">Thêm khách hàng</button>
</div>

<?php global $dsKhachHang; ?>

<!-- DEBUG OUTPUT -->

<div class="card mb-3">
<!-- (debug messages removed) -->

  <div class="card-body">
    <div class="row">
      <div class="col-md-10">
        <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm theo tên khách hàng...">
      </div>
      <div class="col-md-2">
        <button class="btn btn-secondary w-100" id="showAllBtn">Hiển thị tất cả</button>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="tableKhachHang">
      <style>
        /* Force table text to dark in case site CSS sets it to white */
        #tableKhachHang tbody td { color: #000 !important; }
      </style>

      <thead class="table-dark">
        <tr>
          <th>Mã KH</th>
          <th>Họ tên</th>
          <th>Email</th>
          <th>Điện thoại</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($dsKhachHang)): ?>
          <?php foreach($dsKhachHang as $kh): ?>
          <tr data-makh="<?php echo htmlspecialchars($kh['MaKH'], ENT_QUOTES, 'UTF-8'); ?>">
            <td><?php echo htmlspecialchars($kh['MaKH'], ENT_QUOTES); ?></td>
            <td><?php echo htmlspecialchars($kh['HoTen'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($kh['Email'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($kh['DienThoai'], ENT_QUOTES, 'UTF-8'); ?></td>
             <!-- Simplified: Just echo values -->
             <!-- MaKH: <?= isset($kh['MaKH']) ? 'isset' : 'not set' ?> -->
            <td>
              <button class="btn btn-warning btn-sm btn-sua-kh" 
                  data-bs-toggle="modal" data-bs-target="#khachHangModal"
          data-makh="<?php echo htmlspecialchars($kh['MaKH'], ENT_QUOTES, 'UTF-8'); ?>" 
          data-hoten="<?php echo htmlspecialchars($kh['HoTen'], ENT_QUOTES, 'UTF-8'); ?>" 
          data-email="<?php echo htmlspecialchars($kh['Email'], ENT_QUOTES, 'UTF-8'); ?>" 
          data-dienthoai="<?php echo htmlspecialchars($kh['DienThoai'], ENT_QUOTES, 'UTF-8'); ?>">Sửa</button>
        <button class="btn btn-danger btn-sm btn-xoa-kh" data-makh="<?php echo htmlspecialchars($kh['MaKH'], ENT_QUOTES); ?>">Xóa</button>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">Không có dữ liệu</td>
          </tr>
        <?php endif; ?>
      </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal thêm/sửa khách hàng -->
<div class="modal fade" id="khachHangModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="khachHangForm">
        <div class="modal-header">
          <h5 class="modal-title" id="khachHangModalLabel">Thêm khách hàng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="form_MaKH" name="maKH">
          <div class="mb-3">
            <label>Họ tên</label>
            <input type="text" id="form_HoTen" name="hoTen" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" id="form_Email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Điện thoại</label>
            <input type="text" id="form_DienThoai" name="dienThoai" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-primary">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){

  // Tìm kiếm khách hàng
  const searchInput = document.getElementById('searchInput');
  const showAllBtn = document.getElementById('showAllBtn');
  const tableKhachHang = document.getElementById('tableKhachHang');
  
  if(searchInput) {
    searchInput.addEventListener('keyup', function(){
        const searchValue = this.value.toLowerCase();
        const rows = tableKhachHang.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if(cells.length > 0) {
                const hoTen = cells[1].textContent.toLowerCase();
                const email = cells[2].textContent.toLowerCase();
                const dienThoai = cells[3].textContent.toLowerCase();
                
                if(hoTen.includes(searchValue) || email.includes(searchValue) || dienThoai.includes(searchValue)){
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
  }

  // Hiển thị tất cả
  if(showAllBtn) {
    showAllBtn.addEventListener('click', function(){
        if(searchInput) searchInput.value = '';
        const rows = tableKhachHang.querySelectorAll('tbody tr');
        rows.forEach(row => row.style.display = '');
    });
  }

});

// Kiểm tra xem jQuery có load không, nếu có thì dùng jQuery
if(typeof jQuery !== 'undefined') {
  var $ = jQuery;
  $(document).ready(function(){

    // Thêm / Sửa khách hàng
    $('#khachHangForm').on('submit', function(e){
        e.preventDefault();
        var maKH = $('#form_MaKH').val();
        var actionType = (maKH)?'suaKhachHang':'themKhachHang';
        $.ajax({
            url:'controller/khachhangController.php',
            type:'POST',
            data: $(this).serialize()+'&action='+actionType,
            dataType:'json',
            success:function(res){
                if(res.success){
                    alert(res.message);
                    let row = `<tr data-makh="${res.data.MaKH}">
                        <td>${res.data.MaKH}</td>
                        <td>${res.data.HoTen}</td>
                        <td>${res.data.Email}</td>
                        <td>${res.data.DienThoai}</td>
                        <td>
                          <button class="btn btn-warning btn-sm btn-sua-kh" data-bs-toggle="modal" data-bs-target="#khachHangModal"
                              data-makh="${res.data.MaKH}" data-hoten="${res.data.HoTen}" 
                              data-email="${res.data.Email}" data-dienthoai="${res.data.DienThoai}">Sửa</button>
                          <button class="btn btn-danger btn-sm btn-xoa-kh" data-makh="${res.data.MaKH}">Xóa</button>
                        </td>
                    </tr>`;
                    if(maKH) $('#tableKhachHang tbody tr[data-makh="'+maKH+'"]').replaceWith(row);
                    else $('#tableKhachHang tbody').append(row);
                    $('#khachHangModal').modal('hide');
                } else alert('Lỗi: '+res.message);
            }
        });
    });

    // Xóa khách hàng
    $('#tableKhachHang').on('click','.btn-xoa-kh', function(){
        var maKH = $(this).data('makh');
        if(confirm('Bạn có chắc chắn muốn xóa?')){
            $.ajax({
                url: 'controller/khachhangController.php',
                type: 'POST',
                data: { action: 'xoaKhachHang', maKH: maKH },
                dataType: 'json',
                success: function(res){
                    if(res.success){
                        alert(res.message);
                        $('tr[data-makh="'+maKH+'"]').remove();
                    } else {
                        alert('Lỗi: ' + res.message);
                    }
                },
                error: function(){
                    alert('Lỗi kết nối. Không thể xóa.');
                }
            });
        }
    });

    // Khi mở modal sửa, điền dữ liệu
    $('#tableKhachHang').on('click','.btn-sua-kh', function(){
        $('#form_MaKH').val($(this).data('makh'));
        $('#form_HoTen').val($(this).data('hoten'));
        $('#form_Email').val($(this).data('email'));
        $('#form_DienThoai').val($(this).data('dienthoai'));
        $('#khachHangModalLabel').text('Sửa khách hàng');
    });

    // Khi nhấn thêm mới
    $('#khachHangModal').on('show.bs.modal', function(){
        if(!$('#form_MaKH').val()) $('#khachHangModalLabel').text('Thêm khách hàng');
    });

  });
}
</script>
