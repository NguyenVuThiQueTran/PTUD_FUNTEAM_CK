<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Quản Lý Dịch Vụ</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dichVuModal" id="btnThemDichVu">
        <i class="fas fa-plus"></i> Thêm dịch vụ mới
    </button>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form id="formFilterDV" onsubmit="return false;">
            <div class="row">
                <div class="col-md-8">
                    <input type="text" id="searchKeywordDV" class="form-control" placeholder="Tìm kiếm tên dịch vụ...">
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-secondary" id="btnResetFilterDV">
                        Hiển thị tất cả
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="tableDichVu">
            <thead class="table-dark">
                <tr>
                    <th>Mã DV</th>
                    <th>Tên Dịch Vụ</th>
                    <th>Đơn Giá</th>
                    <th>Mô Tả</th> <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dsDichVu)): ?>
                    <?php foreach($dsDichVu as $dv): ?>
                    <tr>
                        <td><?php echo $dv['MaDV']; ?></td>
                        <td><?php echo $dv['TenDV']; ?></td>
                        <td><?php echo number_format($dv['DonGia']); ?> VNĐ</td>
                        <td><?php echo htmlspecialchars($dv['MoTa'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-sua-dv" 
                                    data-bs-toggle="modal" data-bs-target="#dichVuModal"
                                    data-madv="<?php echo $dv['MaDV']; ?>"
                                    data-tendv="<?php echo $dv['TenDV']; ?>"
                                    data-dongia="<?php echo $dv['DonGia']; ?>" data-mota="<?php echo htmlspecialchars($dv['MoTa'], ENT_QUOTES, 'UTF-8'); ?>"> Sửa
                            </button>
                            <button class="btn btn-danger btn-sm btn-xoa-dv" 
                                    data-madv="<?php echo $dv['MaDV']; ?>">
                                Xóa
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Chưa có dữ liệu dịch vụ.</td> </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="dichVuModal" tabindex="-1" aria-labelledby="dichVuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dichVuModalLabel">Thêm dịch vụ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dichVuForm">
                <div class="modal-body">
                    <input type="hidden" id="form_MaDV" name="maDV">
                    
                    <div class="mb-3">
                        <label for="form_TenDV" class="form-label">Tên dịch vụ</label>
                        <input type="text" class="form-control" id="form_TenDV" name="tenDV" required>
                    </div>
                    <div class="mb-3">
                        <label for="form_DonGia" class="form-label">Đơn giá</label>
                        <input type="number" class="form-control" id="form_DonGia" name="donGia" required>
                    </div>
                    <div class="mb-3">
                        <label for="form_MoTa" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="form_MoTa" name="moTa" rows="3"></textarea>
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
    
    // --- PHẦN LỌC (Giữ nguyên) ---
    function filterDichVu(){
        var keyword = $('#searchKeywordDV').val().toLowerCase();
        $('#tableDichVu tbody tr').each(function(){
            var row = $(this);
            var tenDV = row.find('td:eq(1)').text().toLowerCase();
            var show = true;
            if(keyword && !tenDV.includes(keyword)) { show = false; }
            if(show) row.show(); else row.hide();
        });
    }
    $('#searchKeywordDV').on('input change', filterDichVu);
    $('#btnResetFilterDV').click(function(){
        $('#formFilterDV')[0].reset();
        filterDichVu();
    });

    // --- XỬ LÝ CHỨC NĂNG (Đã sửa) ---

    // 1. Thêm
    $('#btnThemDichVu').on('click', function(){
        $('#dichVuForm')[0].reset();
        $('#form_MaDV').val('');
        $('#dichVuModalLabel').text('Thêm dịch vụ mới');
    });

    // 2. Sửa (Đã sửa)
    $('#tableDichVu').on('click', '.btn-sua-dv', function(){
        var maDV = $(this).data('madv');
        var tenDV = $(this).data('tendv');
        var donGia = $(this).data('dongia'); // Sửa 'gia'
        var moTa = $(this).data('mota');     // Thêm 'mota'

        $('#form_MaDV').val(maDV);
        $('#form_TenDV').val(tenDV);
        $('#form_DonGia').val(donGia); // Sửa 'Gia'
        $('#form_MoTa').val(moTa);     // Thêm 'MoTa'
        
        $('#dichVuModalLabel').text('Sửa thông tin dịch vụ');
    });

    // 3. Xóa (Giữ nguyên)
    $('#tableDichVu').on('click', '.btn-xoa-dv', function(){
        var maDV = $(this).data('madv');
        if(confirm('Bạn có chắc chắn muốn xóa dịch vụ ' + maDV + '?')) {
            $.ajax({
                url: 'controller/dichvuController.php',
                type: 'POST',
                data: { action: 'xoaDichVu', maDV: maDV },
                dataType: 'json',
                success: function(response){
                    if(response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + response.message);
                    }
                },
                error: function(){ alert('Lỗi kết nối. Không thể xóa.'); }
            });
        }
    });

    // 4. Submit Form (Giữ nguyên)
    $('#dichVuForm').on('submit', function(e){
        e.preventDefault();
        var maDV = $('#form_MaDV').val();
        var actionType = (maDV) ? 'suaDichVu' : 'themDichVu';
        
        $.ajax({
            url: 'controller/dichvuController.php',
            type: 'POST',
            data: $(this).serialize() + '&action=' + actionType,
            dataType: 'json',
            success: function(response){
                if(response.success) {
                    alert(response.message);
                    $('#dichVuModal').modal('hide');
                    location.reload();
                } else {
                    alert('Lỗi: ' + response.message);
                }
            },
            error: function(){ alert('Lỗi kết nối. Không thể lưu.'); }
        });
    });
});
</script>