<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Quản Lý Phòng</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#phongModal" id="btnThemPhong">
        <i class="fas fa-plus"></i> Thêm phòng mới
    </button>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form id="formFilter" onsubmit="return false;">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" id="searchKeyword" class="form-control" placeholder="Tìm kiếm số phòng...">
                </div>
                <div class="col-md-3">
                    <select id="filterHangPhong" class="form-select">
                        <option value="">Tất cả hạng</option>
                        <?php foreach($hangPhongList as $hang): ?>
                            <option value="<?php echo $hang; ?>"><?php echo $hang; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterTang" class="form-select">
                        <option value="">Tất cả tầng</option>
                        <?php foreach($tangList as $tang): ?>
                            <option value="<?php echo $tang; ?>">Tầng <?php echo $tang; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterTrangThai" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="Đang ở">Đang ở</option>
                        <option value="Trống">Trống</option>
                        <option value="Đã đặt">Đã đặt</option>
                        <option value="Bảo trì">Bảo trì</option>
                    </select>
                </div>
            </div>
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-secondary" id="btnResetFilter">
                    Hiển thị tất cả
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="tablePhong"> <thead class="table-dark">
                <tr>
                    <th>Mã phòng</th>
                    <th>Số phòng</th>
                    <th>Tầng</th>
                    <th>Hạng phòng</th>
                    <th>Sức chứa</th>
                    <th>Đơn giá</th>
                    <th>Trạng thái</th>
                    <th style="width: 220px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dsPhong as $phong): ?>
                <tr>
                    <td><?php echo $phong['MaPhong']; ?></td>
                    <td><?php echo $phong['SoPhong']; ?></td>
                    <td><?php echo $phong['Tang']; ?></td>
                    <td><?php echo $phong['HangPhong']; ?></td>
                    <td><?php echo htmlspecialchars($phong['SucChua']); ?></td>
                    <td><?php echo number_format($phong['DonGia']); ?> VNĐ</td>
                    <td>
                        <span class="badge <?php
                            switch($phong['TrangThai']){
                                case 'Trống': echo 'bg-success'; break;
                                case 'Đang ở': echo 'bg-primary'; break;
                                case 'Đã đặt': echo 'bg-warning'; break;
                                case 'Bảo trì': echo 'bg-danger'; break;
                                default: echo 'bg-secondary';
                            }
                        ?>">
                            <?php echo $phong['TrangThai']; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-sua" 
                                data-bs-toggle="modal" data-bs-target="#phongModal"
                                data-maphong="<?php echo $phong['MaPhong']; ?>"
                                data-sophong="<?php echo $phong['SoPhong']; ?>"
                                data-tang="<?php echo $phong['Tang']; ?>"
                                data-hangphong="<?php echo $phong['HangPhong']; ?>"
                                data-succhua="<?php echo htmlspecialchars($phong['SucChua']); ?>"
                                data-dongia="<?php echo $phong['DonGia']; ?>">
                            Sửa
                        </button>
                        <button class="btn btn-danger btn-sm btn-xoa" 
                                data-maphong="<?php echo $phong['MaPhong']; ?>">
                            Xóa
                        </button>
                        <button class="btn btn-info btn-sm btn-update" 
                                data-maphong="<?php echo $phong['MaPhong']; ?>" 
                                data-trangthai="<?php echo $phong['TrangThai']; ?>">
                            Trạng thái
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="phongModal" tabindex="-1" aria-labelledby="phongModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="phongModalLabel">Thêm phòng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="phongForm">
                <div class="modal-body">
                    <input type="hidden" id="form_MaPhong" name="maPhong">
                    
                    <div class="mb-3">
                        <label for="form_SoPhong" class="form-label">Số phòng</label>
                        <input type="text" class="form-control" id="form_SoPhong" name="soPhong" required>
                    </div>
                    <div class="mb-3">
                        <label for="form_Tang" class="form-label">Tầng</label>
                        <select class="form-select" id="form_Tang" name="tang" required>
                            <option value="">-- Chọn tầng --</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="form_HangPhong" class="form-label">Hạng phòng</label>
                        <select class="form-select" id="form_HangPhong" name="hangPhong" required>
                            <option value="">-- Chọn hạng phòng --</option>
                            <option value="Standard">Standard</option>
                            <option value="Superior">Superior</option>
                            <option value="Deluxe">Deluxe</option>
                            <option value="Suite">Suite</option>
                            <option value="Family">Family</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="form_SucChua" class="form-label">Sức chứa</label>
                        <input type="number" class="form-control" id="form_SucChua" name="sucChua" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="form_DonGia" class="form-label">Đơn giá</label>
                        <input type="number" class="form-control" id="form_DonGia" name="donGia" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    
    // --- PHẦN LỌC (Giữ nguyên) ---
    function filterRooms(){
        var keyword = $('#searchKeyword').val().toLowerCase();
        var hangPhong = $('#filterHangPhong').val();
        var tang = $('#filterTang').val();
        var trangThai = $('#filterTrangThai').val();

        $('#tablePhong tbody tr').each(function(){
            var row = $(this);
            var soPhong = row.find('td:eq(1)').text().toLowerCase();
            var hang = row.find('td:eq(3)').text();
            var tangText = row.find('td:eq(2)').text();
            var trangthai = row.find('td:eq(6)').find('span.badge').text().trim();
            var show = true;

            if(keyword && !soPhong.includes(keyword)) show=false;
            if(hangPhong && hang!=hangPhong) show=false;
            if(tang && tangText!=tang) show=false;
            if(trangThai && trangthai!=trangThai) show=false;

            if(show) row.show(); else row.hide();
        });
    }
    $('#searchKeyword, #filterHangPhong, #filterTang, #filterTrangThai').on('input change', filterRooms);
    $('#btnResetFilter').click(function(){
        $('#formFilter')[0].reset();
        filterRooms();
    });

    
    // --- XỬ LÝ CHỨC NĂNG MỚI (Thêm, Sửa, Xóa) ---

    // 1. Khi nhấn nút "Thêm phòng mới" (Giữ nguyên)
    $('#btnThemPhong').on('click', function(){
        $('#phongForm')[0].reset();
        $('#form_MaPhong').val('');
        $('#form_SucChua').val('');
        $('#phongModalLabel').text('Thêm phòng mới');
    });

    // 2. Khi nhấn nút "Sửa" (Giữ nguyên)
    $('#tablePhong').on('click', '.btn-sua', function(){
        var maPhong = $(this).data('maphong');
        var soPhong = $(this).data('sophong');
        var tang = $(this).data('tang');
        var hangPhong = $(this).data('hangphong');
        var sucChua = $(this).data('succhua');
        var donGia = $(this).data('dongia');

        $('#form_MaPhong').val(maPhong);
        $('#form_SoPhong').val(soPhong);
        $('#form_Tang').val(tang);
        $('#form_HangPhong').val(hangPhong);
        $('#form_SucChua').val(sucChua);
        $('#form_DonGia').val(donGia);
        
        $('#phongModalLabel').text('Sửa thông tin phòng ' + soPhong);
    });

    // 3. Khi nhấn nút "Xóa"
    $('#tablePhong').on('click', '.btn-xoa', function(){
        var maPhong = $(this).data('maphong');
        
            if(confirm('Bạn có chắc chắn muốn xóa phòng ' + maPhong + '?')) {
            $.ajax({
                url: 'controller/phongController.php',
                type: 'POST',
                data: {
                    action: 'xoaPhong',
                    maPhong: maPhong
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

    // 4. Khi nhấn nút "Lưu thay đổi" (Submit form trong Modal)
    $('#phongForm').on('submit', function(e){
        e.preventDefault(); 

        var maPhong = $('#form_MaPhong').val();
        var actionType = (maPhong) ? 'suaPhong' : 'themPhong'; 
        
        $.ajax({
            url: 'controller/phongController.php',
            type: 'POST',
            data: $(this).serialize() + '&action=' + actionType,
            dataType: 'json',
            success: function(response){
                if(response.success) {
                    alert(response.message);
                    $('#phongModal').modal('hide'); 
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

    // 5. Nút Cập nhật trạng thái — sử dụng modal với select để chọn trạng thái
    // Thao tác: khi nhấn 'Trạng thái' sẽ mở modal, chọn 1 trong các giá trị rồi gửi AJAX

    // chèn modal HTML (modal đặt bên trong body, nhưng trước script)
    var trangThaiModalHtml = '\n<div class="modal fade" id="trangThaiModal" tabindex="-1" aria-labelledby="trangThaiModalLabel" aria-hidden="true">\n    <div class="modal-dialog">\n        <div class="modal-content">\n            <div class="modal-header">\n                <h5 class="modal-title" id="trangThaiModalLabel">Cập nhật trạng thái phòng</h5>\n                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\n            </div>\n            <div class="modal-body">\n                <input type="hidden" id="tt_maPhong">\n                <div class="mb-3">\n                    <label for="selectTrangThai" class="form-label">Trạng thái</label>\n                    <select id="selectTrangThai" class="form-select">\n                        <option value="">-- Chọn trạng thái --</option>\n                        <option value="Đang ở">Đang ở</option>\n                        <option value="Trống">Trống</option>\n                        <option value="Đã đặt">Đã đặt</option>\n                        <option value="Bảo trì">Bảo trì</option>\n                    </select>\n                </div>\n            </div>\n            <div class="modal-footer">\n                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>\n                <button type="button" class="btn btn-primary" id="btnSaveTrangThai">Lưu</button>\n            </div>\n        </div>\n    </div>\n</div>\n';

    // thêm modal vào DOM nếu chưa có
    if($('#trangThaiModal').length === 0) {
        $('body').append(trangThaiModalHtml);
    }

    $('#tablePhong').on('click', '.btn-update', function(){
        var maPhong = $(this).data('maphong');
        var currentTrangThai = $(this).data('trangthai');

        $('#tt_maPhong').val(maPhong);
        $('#selectTrangThai').val(currentTrangThai);
        $('#trangThaiModal').data('current', currentTrangThai);

        var modalEl = document.getElementById('trangThaiModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    $('body').on('click', '#btnSaveTrangThai', function(){
        var maPhong = $('#tt_maPhong').val();
        var newTrangThai = $('#selectTrangThai').val();
        var current = $('#trangThaiModal').data('current');
        var modalEl = document.getElementById('trangThaiModal');
        var modal = bootstrap.Modal.getInstance(modalEl);

        if(!newTrangThai) {
            alert('Vui lòng chọn trạng thái.');
            return;
        }

        if(newTrangThai === current) {
            if(modal) modal.hide();
            return;
        }

        $.ajax({
            url: 'controller/phongController.php',
            type: 'POST',
            data: {
                action: 'capNhatTrangThai',
                maPhong: maPhong,
                trangThai: newTrangThai
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert(response.message);
                    if(modal) modal.hide();
                    location.reload();
                } else {
                    alert('Lỗi: ' + response.message);
                }
            },
            error: function() {
                alert('Lỗi kết nối.');
            }
        });
    });

});
</script>