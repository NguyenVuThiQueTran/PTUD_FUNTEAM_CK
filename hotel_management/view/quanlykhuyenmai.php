<div class="d-flex justify-content-between mb-3 align-items-center">
  <h2 class="fw-bold text-dark"><i class="fas fa-tags me-2"></i>Quản Lý Khuyến Mãi</h2>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKM" id="btnThemKM">
      <i class="fas fa-plus me-2"></i>Thêm khuyến mãi
  </button>
</div>

<?php global $dsKhuyenMai; ?>

<div class="card mb-3 shadow-sm border-0">
  <div class="card-body">
    <div class="row">
      <div class="col-md-10">
        <input type="text" id="searchKM" class="form-control" placeholder="Tìm kiếm theo mã KM, tên chương trình...">
      </div>
      <div class="col-md-2">
        <button class="btn btn-secondary w-100" id="resetFilter">Hiển thị tất cả</button>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm border-0">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-bordered mb-0 align-middle" id="tableKM">
        <thead class="table-dark">
          <tr>
            <th class="py-3 ps-3">Mã KM</th>
            <th class="py-3">Tên chương trình</th>
            <th class="py-3 text-center">Mức giảm (%)</th>
            <th class="py-3 text-center">Thời gian hiệu lực</th>
            <th class="py-3 text-center" style="width: 160px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($dsKhuyenMai)): foreach($dsKhuyenMai as $km): ?>
            <tr>
              <td class="ps-3 fw-bold text-center"><?php echo htmlspecialchars($km['maKM']); ?></td>
              <td class="text-dark"><?php echo htmlspecialchars($km['tenCT']); ?></td>
              <td class="text-center text-danger fw-bold"><?php echo htmlspecialchars($km['mucGiam']); ?>%</td>
              <td class="text-center">
                <?php 
                  $start = date('d/m/Y', strtotime($km['ngayBatDau']));
                  $end = date('d/m/Y', strtotime($km['ngayKetThuc']));
                  echo $start . ' - ' . $end;
                ?>
              </td>
              <td class="text-center">
                <button class="btn btn-warning btn-sm me-1 btn-sua-km" 
                    data-bs-toggle="modal" data-bs-target="#modalKM"
                    data-makm="<?php echo $km['maKM']; ?>"
                    data-tenct="<?php echo $km['tenCT']; ?>"
                    data-mucgiam="<?php echo $km['mucGiam']; ?>"
                    data-bd="<?php echo $km['ngayBatDau']; ?>"
                    data-kt="<?php echo $km['ngayKetThuc']; ?>">
                    Sửa
                </button>
                <button class="btn btn-danger btn-sm btn-xoa-km" data-makm="<?php echo $km['maKM']; ?>">
                    Xóa
                </button>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" class="text-center py-4">Chưa có chương trình khuyến mãi nào</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="modalKM" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-dark" id="modalTitle">Thêm Khuyến Mãi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formKM">
        <div class="modal-body">
          <input type="hidden" id="actionType" name="action" value="themKM">
          
          <div class="mb-3">
            <label class="form-label fw-bold">Mã khuyến mãi</label>
            <input type="text" name="maKM" id="maKM" class="form-control" required placeholder="VD: KM001">
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-bold">Tên chương trình</label>
            <input type="text" name="tenCT" id="tenCT" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Mức giảm (%)</label>
            <input type="number" name="mucGiam" id="mucGiam" class="form-control" required min="0" max="100">
          </div>

          <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Ngày bắt đầu</label>
                <input type="date" name="ngayBatDau" id="ngayBatDau" class="form-control" required>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Ngày kết thúc</label>
                <input type="date" name="ngayKetThuc" id="ngayKetThuc" class="form-control" required>
            </div>
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
$(document).ready(function(){
    $('#searchKM').on('keyup', function(){
        var val = $(this).val().toLowerCase();
        $('#tableKM tbody tr').filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1)
        });
    });
    $('#resetFilter').click(function(){
        $('#searchKM').val('');
        $('#tableKM tbody tr').show();
    });

    $('#btnThemKM').click(function(){
        $('#formKM')[0].reset();
        $('#actionType').val('themKM');
        $('#maKM').prop('readonly', false);
        $('#modalTitle').text('Thêm Khuyến Mãi Mới');
    });

    $(document).on('click', '.btn-sua-km', function(){
        var btn = $(this);
        $('#actionType').val('suaKM');
        $('#maKM').val(btn.data('makm')).prop('readonly', true);
        $('#tenCT').val(btn.data('tenct'));
        $('#mucGiam').val(btn.data('mucgiam'));
        $('#ngayBatDau').val(btn.data('bd'));
        $('#ngayKetThuc').val(btn.data('kt'));
        $('#modalTitle').text('Cập Nhật Khuyến Mãi');
    });

    $('#formKM').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: 'controller/khuyenmaiController.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res){
                if(res.success){ alert(res.message); location.reload(); }
                else { alert(res.message); }
            },
            error: function(){ alert('Lỗi kết nối server!'); }
        });
    });

    $(document).on('click', '.btn-xoa-km', function(){
        if(confirm('Bạn có chắc muốn xóa khuyến mãi này?')){
            $.post('controller/khuyenmaiController.php', 
                {action:'xoaKM', maKM: $(this).data('makm')}, 
                function(res){ location.reload(); }, 'json'
            );
        }
    });
});
</script>