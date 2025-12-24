<div class="d-flex justify-content-between mb-3 align-items-center">
  <h2 class="fw-bold text-dark"><i class="fas fa-users me-2"></i>Quản Lý Khách Hàng</h2>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#khachHangModal" id="btnThemKH">
      <i class="fas fa-plus me-2"></i>Thêm khách hàng
  </button>
</div>

<?php global $dsKhachHang; ?>

<div class="card mb-3 shadow-sm border-0">
  <div class="card-body">
    <div class="row">
      <div class="col-md-10">
        <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm theo tên, email, sdt...">
      </div>
      <div class="col-md-2">
        <button class="btn btn-secondary w-100" id="showAllBtn">Hiển thị tất cả</button>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm border-0">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-bordered mb-0 align-middle" id="tableKhachHang">
      
      <thead class="table-dark">
        <tr>
          <th class="py-3 ps-3">Mã KH</th>
          <th class="py-3">Họ tên</th>
          <th class="py-3">CCCD</th>
          <th class="py-3">Email</th>
          <th class="py-3">Điện thoại</th>
          <th class="py-3 text-center" style="width: 150px;">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($dsKhachHang)): ?>
          <?php foreach($dsKhachHang as $kh): 
              $id = isset($kh['idKH']) ? $kh['idKH'] : (isset($kh['MaKH']) ? $kh['MaKH'] : '');
              $ten = isset($kh['hoTen']) ? $kh['hoTen'] : (isset($kh['HoTen']) ? $kh['HoTen'] : '');
              $email = isset($kh['email']) ? $kh['email'] : (isset($kh['Email']) ? $kh['Email'] : '');
              
              $sdt = '';
              if(isset($kh['soDienThoai'])) $sdt = $kh['soDienThoai'];
              elseif(isset($kh['SoDienThoai'])) $sdt = $kh['SoDienThoai'];
              elseif(isset($kh['DienThoai'])) $sdt = $kh['DienThoai'];

              $cccd = isset($kh['CCCD']) ? $kh['CCCD'] : (isset($kh['cccd']) ? $kh['cccd'] : '');
          ?>
          <tr data-makh="<?php echo htmlspecialchars($id); ?>">
            <td class="ps-3 text-center"><?php echo htmlspecialchars($id); ?></td>
            <td class="text-dark"><?php echo htmlspecialchars($ten); ?></td>
            <td><?php echo htmlspecialchars($cccd); ?></td>
            <td><?php echo htmlspecialchars($email); ?></td>
            <td><?php echo htmlspecialchars($sdt); ?></td>
            
            <td class="text-center">
              <button class="btn btn-warning btn-sm btn-sua-kh me-1" 
                  data-bs-toggle="modal" data-bs-target="#khachHangModal"
                  data-makh="<?php echo htmlspecialchars($id); ?>" 
                  data-hoten="<?php echo htmlspecialchars($ten); ?>" 
                  data-email="<?php echo htmlspecialchars($email); ?>" 
                  data-sdt="<?php echo htmlspecialchars($sdt); ?>"
                  data-cccd="<?php echo htmlspecialchars($cccd); ?>">
                  Sửa
              </button>
              <button class="btn btn-danger btn-sm btn-xoa-kh" data-makh="<?php echo htmlspecialchars($id); ?>">
                  Xóa
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center py-4">Không có dữ liệu hiển thị</td></tr>
        <?php endif; ?>
      </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="khachHangModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header"> 
        <h5 class="modal-title fw-bold text-dark" id="khachHangModalLabel">Thêm khách hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="khachHangForm">
        <div class="modal-body">
          <input type="hidden" id="form_MaKH" name="maKH">
          
          <div class="mb-3">
            <label class="form-label fw-bold">Họ tên</label>
            <input type="text" id="form_HoTen" name="hoTen" class="form-control" required>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-bold">CCCD</label>
            <input type="text" id="form_CCCD" name="cccd" class="form-control" required placeholder="Nhập số căn cước">
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Email</label>
            <input type="email" id="form_Email" name="email" class="form-control">
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-bold">Điện thoại</label>
            <input type="text" id="form_DienThoai" name="dienThoai" class="form-control" required pattern="[0-9]{10}" title="Vui lòng nhập đúng 10 chữ số">
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
// Tìm kiếm nhanh
document.addEventListener('DOMContentLoaded', function(){
  const searchInput = document.getElementById('searchInput');
  const showAllBtn = document.getElementById('showAllBtn');
  const tableKhachHang = document.getElementById('tableKhachHang');
  
  if(searchInput) {
    searchInput.addEventListener('keyup', function(){
        const searchValue = this.value.toLowerCase();
        const rows = tableKhachHang.querySelectorAll('tbody tr');
        rows.forEach(row => {
            if(row.textContent.toLowerCase().includes(searchValue)) row.style.display = '';
            else row.style.display = 'none';
        });
    });
  }
  if(showAllBtn) {
    showAllBtn.addEventListener('click', function(){
        if(searchInput) searchInput.value = '';
        const rows = tableKhachHang.querySelectorAll('tbody tr');
        rows.forEach(row => row.style.display = '');
    });
  }
});

$(document).ready(function(){
    // Reset Form khi bấm Thêm
    $('#btnThemKH').click(function(){
        $('#khachHangForm')[0].reset();
        $('#form_MaKH').val('');
        $('#khachHangModalLabel').text('Thêm khách hàng mới');
    });

    // Đổ dữ liệu khi bấm Sửa
    $(document).on('click', '.btn-sua-kh', function(){
        $('#form_MaKH').val($(this).data('makh'));
        $('#form_HoTen').val($(this).data('hoten'));
        $('#form_Email').val($(this).data('email'));
        $('#form_DienThoai').val($(this).data('sdt'));
        $('#form_CCCD').val($(this).data('cccd'));
        $('#khachHangModalLabel').text('Cập nhật khách hàng');
    });

    // Submit Form
    $('#khachHangForm').on('submit', function(e){
        e.preventDefault();
        var maKH = $('#form_MaKH').val();
        var actionType = (maKH) ? 'suaKhachHang' : 'themKhachHang';
        
        $.ajax({
            url:'controller/khachhangController.php',
            type:'POST',
            data: $(this).serialize()+'&action='+actionType,
            dataType:'json',
            success:function(res){
                var resp = (typeof res === 'object') ? res : JSON.parse(res);
                if(resp.success){
                    alert(resp.message);
                    location.reload(); 
                } else {
                    alert(resp.message);
                }
            },
            error: function() {
                alert("Lỗi kết nối hoặc lỗi server!");
            }
        });
    });

    // Xóa
    $(document).on('click', '.btn-xoa-kh', function(){
        var maKH = $(this).data('makh');
        if(confirm('Bạn có chắc chắn muốn xóa?')){
            $.post('controller/khachhangController.php', { action: 'xoaKhachHang', maKH: maKH }, function(res){
                var resp = (typeof res === 'object') ? res : JSON.parse(res);
                if(resp.success){
                    $('tr[data-makh="'+maKH+'"]').remove();
                } else {
                    alert('Lỗi: ' + resp.message);
                }
            });
        }
    });
});
</script>