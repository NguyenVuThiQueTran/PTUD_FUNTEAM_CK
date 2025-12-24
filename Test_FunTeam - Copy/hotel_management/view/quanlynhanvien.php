<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Quản Lý Nhân sự</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nhanVienModal" id="btnThemNhanVien">
        <i class="fas fa-plus"></i> Thêm nhân viên mới
    </button>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form id="formFilterNV" onsubmit="return false;">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" id="searchKeywordNV" class="form-control" placeholder="Tìm kiếm tên hoặc SĐT...">
                </div>
                <div class="col-md-4">
                    <select id="filterChucVu" class="form-select">
                        <option value="">Tất cả chức vụ</option>
                        <?php foreach($dsChucVu as $cv): ?>
                            <option value="<?php echo $cv['ChucVu']; ?>"><?php echo $cv['ChucVu']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-secondary" id="btnResetFilterNV">
                        Hiển thị tất cả
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="tableNhanVien">
            <thead class="table-dark">
                <tr>
                    <th>Mã NV</th>
                    <th>Họ Tên</th>
                    <th>Chức vụ</th>
                    <th>Điện thoại</th>
                    <th>Email</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dsNhanVien)): ?>
                    <?php foreach($dsNhanVien as $nv): ?>
                    <tr>
                        <td><?php echo $nv['MaNV']; ?></td>
                        <td><?php echo $nv['HoTen']; ?></td>
                        <td><?php echo $nv['ChucVu']; ?></td>
                        <td><?php echo $nv['DienThoai']; ?></td>
                        <td><?php echo $nv['Email']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-sua-nv" 
                                    data-bs-toggle="modal" data-bs-target="#nhanVienModal"
                                    data-manv="<?php echo $nv['MaNV']; ?>"
                                    data-hoten="<?php echo $nv['HoTen']; ?>"
                                    data-chucvu="<?php echo $nv['ChucVu']; ?>"
                                    data-dienthoai="<?php echo $nv['DienThoai']; ?>"
                                    data-email="<?php echo $nv['Email']; ?>">
                                Sửa
                            </button>
                            <button class="btn btn-danger btn-sm btn-xoa-nv" 
                                    data-manv="<?php echo $nv['MaNV']; ?>">
                                Xóa
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Chưa có dữ liệu nhân viên.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="nhanVienModal" tabindex="-1" aria-labelledby="nhanVienModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nhanVienModalLabel">Thêm nhân viên mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="nhanVienForm">
                <div class="modal-body">
                    <input type="hidden" id="form_MaNV" name="maNV">
                    
                    <div class="mb-3">
                        <label for="form_HoTen" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="form_HoTen" name="hoTen" required>
                    </div>
                    <div class="mb-3">
                        <label for="form_ChucVu" class="form-label">Chức vụ</label>
                        <select class="form-select" id="form_ChucVu" name="chucVu" required>
                            <option value="">-- Chọn chức vụ --</option>
                            <option value="Quản lý">Quản lý</option>
                            <option value="Nhân viên">Nhân viên</option>
                            <option value="Buồng phòng">Buồng phòng</option>
                            <option value="Kế toán">Kế toán</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="form_DienThoai" class="form-label">Điện thoại</label>
                        <input type="text" class="form-control" id="form_DienThoai" name="dienThoai" required>
                    </div>
                    <div class="mb-3">
                        <label for="form_Email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="form_Email" name="email" required>
                    </div>
                    <div class="mb-3" id="wrapMatKhau">
                        <label for="form_MatKhau" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="form_MatKhau" name="matKhau">
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
    
    // --- PHẦN LỌC ---
    function filterNhanVien(){
        var keyword = $('#searchKeywordNV').val().toLowerCase();
        var chucVu = $('#filterChucVu').val();

        $('#tableNhanVien tbody tr').each(function(){
            var row = $(this);
            var hoTen = row.find('td:eq(1)').text().toLowerCase();
            var dienThoai = row.find('td:eq(3)').text().toLowerCase();
            var cv = row.find('td:eq(2)').text();
            
            var show = true;

            // Lọc theo keyword (tên HOẶC sđt)
            if(keyword && !hoTen.includes(keyword) && !dienThoai.includes(keyword)) {
                show = false;
            }
            // Lọc theo chức vụ
            if(chucVu && cv != chucVu) {
                show = false;
            }

            if(show) row.show(); else row.hide();
        });
    }
    $('#searchKeywordNV, #filterChucVu').on('input change', filterNhanVien);
    $('#btnResetFilterNV').click(function(){
        $('#formFilterNV')[0].reset();
        filterNhanVien();
    });

    // --- XỬ LÝ CHỨC NĂNG (Thêm, Sửa, Xóa) ---

    // 1. Khi nhấn nút "Thêm nhân viên mới"
    $('#btnThemNhanVien').on('click', function(){
        $('#nhanVienForm')[0].reset();
        $('#form_MaNV').val('');
        $('#nhanVienModalLabel').text('Thêm nhân viên mới');
        $('#wrapMatKhau').show(); // Hiện trường mật khẩu
        $('#form_MatKhau').prop('required', true); // Bắt buộc nhập MK
    });

    // 2. Khi nhấn nút "Sửa"
    $('#tableNhanVien').on('click', '.btn-sua-nv', function(){
        // Lấy data từ nút
        var maNV = $(this).data('manv');
        var hoTen = $(this).data('hoten');
        var chucVu = $(this).data('chucvu');
        var dienThoai = $(this).data('dienthoai');
        var email = $(this).data('email');

        // Điền data vào form
        $('#form_MaNV').val(maNV);
        $('#form_HoTen').val(hoTen);
        $('#form_ChucVu').val(chucVu);
        $('#form_DienThoai').val(dienThoai);
        $('#form_Email').val(email);
        
        $('#nhanVienModalLabel').text('Sửa thông tin nhân viên');
        $('#wrapMatKhau').hide(); // Ẩn trường mật khẩu khi sửa
        $('#form_MatKhau').prop('required', false); // Không bắt buộc
    });

    // 3. Khi nhấn nút "Xóa"
    $('#tableNhanVien').on('click', '.btn-xoa-nv', function(){
        var maNV = $(this).data('manv');
        
        if(confirm('Bạn có chắc chắn muốn xóa nhân viên ' + maNV + '?')) {
            $.ajax({
                url: 'controller/nhanvienController.php', // Đường dẫn tương đối
                type: 'POST',
                data: {
                    action: 'xoaNhanVien',
                    maNV: maNV
                },
                dataType: 'json',
                success: function(response){
                    if(response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + response.message);
                    }
                },
                error: function(){
                    alert('Lỗi kết nối. Không thể xóa.');
                }
            });
        }
    });

    // 4. Khi nhấn nút "Lưu" (Submit form trong Modal)
    $('#nhanVienForm').on('submit', function(e){
        e.preventDefault();

        var maNV = $('#form_MaNV').val();
        var actionType = (maNV) ? 'suaNhanVien' : 'themNhanVien';
        
        $.ajax({
            url: 'controller/nhanvienController.php', // Đường dẫn tương đối
            type: 'POST',
            data: $(this).serialize() + '&action=' + actionType,
            dataType: 'json',
            success: function(response){
                if(response.success) {
                    alert(response.message);
                    $('#nhanVienModal').modal('hide');
                    location.reload();
                } else {
                    alert('Lỗi: ' + response.message);
                }
            },
            error: function(){
                alert('Lỗi kết nối. Không thể lưu.');
            }
        });
    });
});
</script>