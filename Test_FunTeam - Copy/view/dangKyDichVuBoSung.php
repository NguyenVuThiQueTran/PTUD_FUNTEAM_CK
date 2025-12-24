<?php
// view/dangKyDichVuBoSung.php
session_start();
header('Content-Type: text/html; charset=utf-8');

// Ngăn cache để luôn lấy data mới
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check login
if (!isset($_SESSION['idKH'])) {
    header('Location: login.php');
    exit();
}

// Include model
require_once("../model/clsDichVu.php");

$model = new clsDichVu();

// Get filters
$loaiDV = isset($_GET['loaiDV']) ? $_GET['loaiDV'] : '';
$khoangGia = isset($_GET['khoangGia']) ? $_GET['khoangGia'] : '';

// Parse price range
$giaMin = null;
$giaMax = null;
if ($khoangGia) {
    $parts = explode('-', $khoangGia);
    $giaMin = isset($parts[0]) ? intval($parts[0]) : null;
    $giaMax = isset($parts[1]) ? intval($parts[1]) : null;
}

// Get data
$danhSachDichVu = $model->layDanhSachDichVu($loaiDV, $giaMin, $giaMax);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dịch Vụ Bổ Sung</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
        }
        
        /* Ensure header takes full width */
        #header {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        .container {
            display: flex;
            max-width: 1400px;
            margin: 20px auto;
            gap: 25px;
            padding: 0 20px;
        }
        
        /* SIDEBAR - BÊN TRÁI */
        .sidebar {
            width: 250px;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .filter-section {
            margin-bottom: 25px;
        }
        
        .filter-section h4 {
            font-size: 1rem;
            margin-bottom: 12px;
            color: #555;
        }
        
        .filter-option {
            display: block;
            padding: 8px 0;
        }
        
        .filter-option input {
            margin-right: 8px;
        }
        
        .filter-option label {
            cursor: pointer;
            font-size: 0.95rem;
        }
        
        .btn-apply {
            width: 100%;
            padding: 12px;
            background: #6667ab;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        
        .btn-apply:hover {
            background: #5558a0;
        }
        
        .btn-reset {
            width: 100%;
            padding: 10px;
            background: white;
            color: #666;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 8px;
        }
        
        /* MAIN CONTENT - BÊN PHẢI */
        .main-content {
            flex: 1;
        }
        
        .page-title {
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: #333;
        }
        
        /* GRID 4 CARDS/HÀNG */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        
        .service-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            cursor: pointer;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .service-image {
            width: 100%;
            height: 150px;
            background: linear-gradient(135deg, #6667ab, #8b5bd3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        
        .service-body {
            padding: 15px;
        }
        
        .service-name {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            min-height: 40px;
        }
        
        .service-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 8px;
        }
        
        .service-time {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 8px;
        }
        
        .service-rating {
            color: #ffc107;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .service-status {
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .service-status.available {
            color: #28a745;
        }
        
        .service-status.out {
            color: #dc3545;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .services-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 900px) {
            .services-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 600px) {
            .services-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div id="header">
        <?php include('../layout/header_kh.php'); ?>
    </div>
    
    <div class="container">
        <!-- SIDEBAR - BÊN TRÁI -->
        <div class="sidebar">
            <form method="GET" action="dangKyDichVuBoSung.php">
                <!-- Loại dịch vụ -->
                <div class="filter-section">
                    <h4>Loại dịch vụ</h4>
                    <div class="filter-option">
                        <input type="radio" name="loaiDV" value="" id="all" <?php echo $loaiDV === '' ? 'checked' : ''; ?>>
                        <label for="all">Tất cả</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" name="loaiDV" value="Food" id="food" <?php echo $loaiDV === 'Food' ? 'checked' : ''; ?>>
                        <label for="food">Food</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" name="loaiDV" value="Minibar" id="minibar" <?php echo $loaiDV === 'Minibar' ? 'checked' : ''; ?>>
                        <label for="minibar">Minibar</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" name="loaiDV" value="Spa" id="spa" <?php echo $loaiDV === 'Spa' ? 'checked' : ''; ?>>
                        <label for="spa">Spa</label>
                    </div>
                </div>
                
                <!-- Khoảng giá -->
                <div class="filter-section">
                    <h4>Khoảng giá</h4>
                    <div class="filter-option">
                        <input type="radio" name="khoangGia" value="" id="price_all" <?php echo $khoangGia === '' ? 'checked' : ''; ?>>
                        <label for="price_all">Tất cả</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" name="khoangGia" value="0-500000" id="price1" <?php echo $khoangGia === '0-500000' ? 'checked' : ''; ?>>
                        <label for="price1">0 - 500.000</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" name="khoangGia" value="500000-1000000" id="price2" <?php echo $khoangGia === '500000-1000000' ? 'checked' : ''; ?>>
                        <label for="price2">500.000 - 1.000.000</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" name="khoangGia" value="1000000-1500000" id="price3" <?php echo $khoangGia === '1000000-1500000' ? 'checked' : ''; ?>>
                        <label for="price3">1.000.000 - 1.500.000</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" name="khoangGia" value="1500000-2000000" id="price4" <?php echo $khoangGia === '1500000-2000000' ? 'checked' : ''; ?>>
                        <label for="price4">1.500.000 - 2.000.000</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" name="khoangGia" value="2000000-9999999" id="price5" <?php echo $khoangGia === '2000000-9999999' ? 'checked' : ''; ?>>
                        <label for="price5">Trên 2.000.000</label>
                    </div>
                </div>
                
                <button type="submit" class="btn-apply">Áp dụng</button>
                <button type="button" class="btn-reset" onclick="window.location.href='dangKyDichVuBoSung.php'">Đặt lại</button>
            </form>
        </div>
        
        <!-- MAIN CONTENT - BÊN PHẢI -->
        <div class="main-content">
            <h1 class="page-title">Dịch Vụ Bổ Sung</h1>
            
            <!-- GRID 4 CARDS/HÀNG -->
            <?php if (count($danhSachDichVu) > 0): ?>
                <div class="services-grid">
                    <?php foreach($danhSachDichVu as $dv): 
                        $available = intval($dv['soLuongKhaDung']);
                    ?>
                        <div class="service-card" onclick="window.location.href='chiTietDichVu.php?maDV=<?php echo urlencode($dv['maDV']); ?>'">
                            <!-- Hình -->
                            <div class="service-image">
                                <i class="fas fa-concierge-bell"></i>
                            </div>
                            
                            <div class="service-body">
                                <!-- Tên dịch vụ -->
                                <div class="service-name"><?php echo htmlspecialchars($dv['tenDV']); ?></div>
                                
                                <!-- Giá -->
                                <div class="service-price"><?php echo number_format($dv['donGia'], 0, ',', '.'); ?>đ</div>
                                
                                <!-- Khung giờ -->
                                <div class="service-time">Khung giờ: 24/7</div>
                                
                                <!-- Rating -->
                                <div class="service-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half"></i>
                                </div>
                                
                                <!-- Trạng thái -->
                                <div class="service-status <?php echo $available > 0 ? 'available' : 'out'; ?>">
                                    <?php echo $available > 0 ? "Còn" : 'Hết hàng'; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div style="text-align: center; padding: 100px 20px; background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <i class="fas fa-search" style="font-size: 80px; color: #ddd; margin-bottom: 20px;"></i>
                    <h3 style="color: #666; margin-bottom: 10px;">Không có dịch vụ phù hợp</h3>
                    <p style="color: #999;">Vui lòng thử điều chỉnh bộ lọc để xem thêm dịch vụ khác</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="../BOOTSTRAP/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>