-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 24, 2025 at 02:42 AM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `quanlykhachsan`
--

-- --------------------------------------------------------

--
-- Table structure for table `backup_dichvu`
--

CREATE TABLE `backup_dichvu` (
  `maDV` varchar(20) collate utf8_unicode_ci NOT NULL,
  `tenDV` varchar(100) collate utf8_unicode_ci default NULL,
  `loaiDV` varchar(100) collate utf8_unicode_ci default NULL,
  `donGia` decimal(10,2) default NULL,
  `trangThai` varchar(50) collate utf8_unicode_ci default 'HoatDong',
  `moTa` varchar(200) collate utf8_unicode_ci default NULL,
  `rating` int(11) default '5',
  `soLuongConLai` int(11) default '100'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `backup_dichvu`
--

INSERT INTO `backup_dichvu` (`maDV`, `tenDV`, `loaiDV`, `donGia`, `trangThai`, `moTa`, `rating`, `soLuongConLai`) VALUES
('DV001', 'Buffet sáng', 'Ăn uống', '200000.00', 'HoatDong', 'Buffet sáng quốc tế', 5, 100),
('DV002', 'Massage', 'Spa', '500000.00', 'HoatDong', 'Massage toàn thân', 5, 97),
('DV003', 'Xe đưa đón', 'Vận chuyển', '300000.00', 'HoatDong', 'Xe 7 chỗ', 5, 100),
('DV004', 'Tour du lịch', 'Giải trí', '1000000.00', 'HoatDong', 'Tour đảo Phú Quốc', 5, 98),
('DV005', 'Giặt ủi', 'Tiện ích', '100000.00', 'HoatDong', 'Giặt ủi nhanh', 5, 100);

-- --------------------------------------------------------

--
-- Table structure for table `backup_hd_dichvu`
--

CREATE TABLE `backup_hd_dichvu` (
  `maHD` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `maDV` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `soLuong` int(11) default NULL,
  `donGia` decimal(10,2) default NULL,
  `thanhTien` decimal(15,2) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `backup_hd_dichvu`
--

INSERT INTO `backup_hd_dichvu` (`maHD`, `maDV`, `soLuong`, `donGia`, `thanhTien`) VALUES
('HD001', 'DV001', 2, '200000.00', '400000.00'),
('HD001', 'DV002', 1, '500000.00', '500000.00'),
('HD002', 'DV003', 1, '300000.00', '300000.00'),
('HD003', 'DV004', 2, '1000000.00', '2000000.00'),
('HD004', 'DV005', 3, '100000.00', '300000.00');

-- --------------------------------------------------------

--
-- Table structure for table `backup_hoadon`
--

CREATE TABLE `backup_hoadon` (
  `maHD` varchar(20) collate utf8_unicode_ci NOT NULL,
  `maDDP` varchar(20) collate utf8_unicode_ci default NULL,
  `idKH` int(11) default NULL,
  `ngayLap` date default NULL,
  `phuongThucThanhToan` varchar(100) collate utf8_unicode_ci default NULL,
  `ngayThanhToan` date default NULL,
  `tongTien` decimal(15,2) default NULL,
  `tienPhong` decimal(15,2) default '0.00',
  `tienDichVu` decimal(15,2) default '0.00',
  `tienBoiThuong` decimal(15,2) default '0.00',
  `giamGia` decimal(15,2) default '0.00',
  `noiDungChuyenKhoan` varchar(200) collate utf8_unicode_ci default NULL,
  `trangThai` varchar(50) collate utf8_unicode_ci default 'ChuaThanhToan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `backup_hoadon`
--


-- --------------------------------------------------------

--
-- Table structure for table `boithuong`
--

CREATE TABLE `boithuong` (
  `maBT` varchar(20) collate utf8_unicode_ci NOT NULL,
  `maPhong` varchar(20) collate utf8_unicode_ci default NULL,
  `ngayBT` date default NULL,
  `lyDo` varchar(200) collate utf8_unicode_ci default NULL,
  `tongBoiThuong` decimal(15,2) default NULL,
  PRIMARY KEY  (`maBT`),
  KEY `maPhong` (`maPhong`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `boithuong`
--


-- --------------------------------------------------------

--
-- Table structure for table `chitietboithuong`
--

CREATE TABLE `chitietboithuong` (
  `maBT` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `tenDoVat` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `soLuong` int(11) default NULL,
  `donGia` decimal(10,2) default NULL,
  `tongTien` decimal(15,2) default NULL,
  PRIMARY KEY  (`maBT`,`tenDoVat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `chitietboithuong`
--


-- --------------------------------------------------------

--
-- Table structure for table `chitietdatphong`
--

CREATE TABLE `chitietdatphong` (
  `maDDP` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `maPhong` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`maDDP`,`maPhong`),
  KEY `maPhong` (`maPhong`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `chitietdatphong`
--

INSERT INTO `chitietdatphong` (`maDDP`, `maPhong`) VALUES
('DDP001', 'P101'),
('DDP002', 'P201'),
('DDP003', 'P301'),
('DDP004', 'P401'),
('DDP005', 'P501');

-- --------------------------------------------------------

--
-- Table structure for table `dichvu`
--

CREATE TABLE `dichvu` (
  `maDV` varchar(20) collate utf8_unicode_ci NOT NULL,
  `tenDV` varchar(100) collate utf8_unicode_ci default NULL,
  `loaiDV` varchar(100) collate utf8_unicode_ci default NULL,
  `donGia` decimal(10,2) default NULL,
  `trangThai` varchar(50) collate utf8_unicode_ci default 'HoatDong',
  `moTa` varchar(200) collate utf8_unicode_ci default NULL,
  `rating` int(11) default '5',
  `soLuongConLai` int(11) default '100',
  PRIMARY KEY  (`maDV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dichvu`
--

INSERT INTO `dichvu` (`maDV`, `tenDV`, `loaiDV`, `donGia`, `trangThai`, `moTa`, `rating`, `soLuongConLai`) VALUES
('DV001', 'Buffet sáng', 'Ăn uống', '200000.00', 'HoatDong', 'Buffet sáng quốc tế', 5, 100),
('DV002', 'Massage', 'Spa', '500000.00', 'HoatDong', 'Massage toàn thân', 5, 97),
('DV003', 'Xe đưa đón', 'Vận chuyển', '300000.00', 'HoatDong', 'Xe 7 chỗ', 5, 100),
('DV004', 'Tour du lịch', 'Giải trí', '1000000.00', 'HoatDong', 'Tour đảo Phú Quốc', 5, 98),
('DV005', 'Giặt ủi', 'Tiện ích', '100000.00', 'HoatDong', 'Giặt ủi nhanh', 5, 100);

-- --------------------------------------------------------

--
-- Table structure for table `doan`
--

CREATE TABLE `doan` (
  `maDoan` varchar(20) collate utf8_unicode_ci NOT NULL,
  `tenDoan` varchar(100) collate utf8_unicode_ci default NULL,
  `email` varchar(100) collate utf8_unicode_ci default NULL,
  `matKhau` varchar(255) collate utf8_unicode_ci default NULL,
  `soLuong` int(11) default NULL,
  `thongTinLienHe` varchar(200) collate utf8_unicode_ci default NULL,
  `vaiTro` varchar(50) collate utf8_unicode_ci default 'Doan',
  `trangThai` varchar(50) collate utf8_unicode_ci default 'HoatDong',
  PRIMARY KEY  (`maDoan`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `doan`
--

INSERT INTO `doan` (`maDoan`, `tenDoan`, `email`, `matKhau`, `soLuong`, `thongTinLienHe`, `vaiTro`, `trangThai`) VALUES
('D2390', 'Big', 'big884@doan.com', 'b514ae2faccb0623480d1286dd655176', 1, '34', 'Doan', 'HoatDong'),
('D4359', 'Big Dream', 'bigdream394@doan.com', 'dbf1f034ae89ece73b4f79ca6e12463d', 1, '1234567890', 'Doan', 'HoatDong'),
('D4803', 'Big', 'big710@doan.com', 'f7881e76f3f2a6e3f078a9c5e4ad8285', 1, '34', 'Doan', 'HoatDong'),
('D5757', 'Big', 'big283@doan.com', '45bbfa6328a868108d065bcbcee0c108', 1, '34', 'Doan', 'HoatDong'),
('D6485', 'Funteam', 'funteam673@doan.com', '350170bbe30d4b55e73056a6cbdf3274', 1, '12345678', 'Doan', 'HoatDong'),
('D8830', 'Big Dream', 'bigdream893@doan.com', 'd836658f57be4ddfb92c9637a08d48f0', 1, '1234567890', 'Doan', 'HoatDong'),
('D8932', 'Big', 'big477@doan.com', 'a66af878b51fd9e761a25ce9d553dfd5', 1, '1234567890', 'Doan', 'HoatDong'),
('D9169', 'Funteam', 'funteam610@doan.com', '85a8d335b6de769f6630aa0ae24eef12', 1, '12345678', 'Doan', 'HoatDong'),
('DOAN20251201202128', 'D8049', 'Fun', '9037a9347034659e596b101c2cb7a33d', 1, '0987654321', 'Doan', 'HoatDong');

-- --------------------------------------------------------

--
-- Table structure for table `dondatphong`
--

CREATE TABLE `dondatphong` (
  `maDDP` varchar(20) collate utf8_unicode_ci NOT NULL,
  `idKH` int(11) default NULL,
  `idUser` int(11) default NULL,
  `maPhong` varchar(20) collate utf8_unicode_ci default NULL,
  `maKM` varchar(20) collate utf8_unicode_ci default NULL,
  `ngayDatPhong` date default NULL,
  `ngayNhanPhong` date default NULL,
  `ngayTraPhong` date default NULL,
  `soLuong` int(11) default NULL,
  `ghiChu` varchar(200) collate utf8_unicode_ci default NULL,
  `trangThai` enum('DangCho','DaNhan','DaTra','DaHuy') collate utf8_unicode_ci NOT NULL default 'DangCho',
  `idNhanVien` int(11) default NULL,
  PRIMARY KEY  (`maDDP`),
  KEY `idKH` (`idKH`),
  KEY `maKM` (`maKM`),
  KEY `idx_dondatphong_idUser` (`idUser`),
  KEY `idx_dondatphong_maPhong` (`maPhong`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dondatphong`
--

INSERT INTO `dondatphong` (`maDDP`, `idKH`, `idUser`, `maPhong`, `maKM`, `ngayDatPhong`, `ngayNhanPhong`, `ngayTraPhong`, `soLuong`, `ghiChu`, `trangThai`, `idNhanVien`) VALUES
('DDP001', 1, NULL, NULL, 'KM001', '2024-01-10', '2024-12-25', '2024-12-27', 1, 'Phòng đơn', 'DaTra', NULL),
('DDP002', 2, NULL, NULL, 'KM002', '2024-01-11', '2024-12-26', '2024-12-28', 2, 'Phòng đôi', 'DaNhan', NULL),
('DDP003', 3, NULL, NULL, 'KM003', '2024-01-12', '2024-12-27', '2024-12-29', 1, 'Phòng cao cấp', 'DangCho', NULL),
('DDP004', 4, NULL, NULL, 'KM001', '2024-01-13', '2024-12-28', '2024-12-30', 2, '2 phòng', 'DaTra', NULL),
('DDP005', 5, NULL, NULL, NULL, '2024-01-14', '2024-12-29', '2024-12-31', 1, 'Không KM', 'DaHuy', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hd_dichvu`
--

CREATE TABLE `hd_dichvu` (
  `maHD` varchar(20) collate utf8_unicode_ci NOT NULL,
  `maDV` varchar(20) collate utf8_unicode_ci NOT NULL,
  `soLuong` int(11) default NULL,
  `donGia` decimal(10,2) default NULL,
  `thanhTien` decimal(15,2) default NULL,
  PRIMARY KEY  (`maHD`,`maDV`),
  KEY `maDV` (`maDV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `hd_dichvu`
--

INSERT INTO `hd_dichvu` (`maHD`, `maDV`, `soLuong`, `donGia`, `thanhTien`) VALUES
('HD001', 'DV001', 2, '200000.00', '400000.00'),
('HD001', 'DV002', 1, '500000.00', '500000.00'),
('HD002', 'DV003', 1, '300000.00', '300000.00'),
('HD003', 'DV004', 2, '1000000.00', '2000000.00'),
('HD004', 'DV005', 3, '100000.00', '300000.00');

-- --------------------------------------------------------

--
-- Table structure for table `hoadon`
--

CREATE TABLE `hoadon` (
  `maHD` varchar(20) collate utf8_unicode_ci NOT NULL,
  `maDDP` varchar(20) collate utf8_unicode_ci default NULL,
  `idKH` int(11) NOT NULL,
  `ngayLap` date default NULL,
  `phuongThucThanhToan` varchar(100) collate utf8_unicode_ci default NULL,
  `ngayThanhToan` date default NULL,
  `tienPhong` decimal(15,2) default '0.00',
  `tienDichVu` decimal(15,2) default '0.00',
  `tienBoiThuong` decimal(15,2) default '0.00',
  `giamGia` decimal(15,2) default '0.00',
  `tongTien` decimal(15,2) default NULL,
  `noiDungChuyenKhoan` varchar(200) collate utf8_unicode_ci default NULL,
  `trangThai` varchar(50) collate utf8_unicode_ci default 'ChuaThanhToan',
  PRIMARY KEY  (`maHD`),
  KEY `maDDP` (`maDDP`),
  KEY `idx_hoadon_idKH` (`idKH`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `hoadon`
--

INSERT INTO `hoadon` (`maHD`, `maDDP`, `idKH`, `ngayLap`, `phuongThucThanhToan`, `ngayThanhToan`, `tienPhong`, `tienDichVu`, `tienBoiThuong`, `giamGia`, `tongTien`, `noiDungChuyenKhoan`, `trangThai`) VALUES
('HD001', 'DDP001', 1, '2025-12-25', 'Tiền mặt', '2025-12-25', '1500000.00', '900000.00', '0.00', '100000.00', '2300000.00', NULL, 'DaThanhToan'),
('HD002', 'DDP002', 2, '2025-12-26', 'Chuyển khoản', '2025-12-26', '2400000.00', '300000.00', '0.00', '150000.00', '2550000.00', 'Thanh toán hóa đơn HD002', 'DaThanhToan'),
('HD003', 'DDP003', 3, '2025-12-27', 'Thẻ tín dụng', NULL, '1800000.00', '2000000.00', '0.00', '200000.00', '3600000.00', NULL, 'ChuaThanhToan'),
('HD004', 'DDP004', 4, '2025-12-28', 'Tiền mặt', '2025-12-28', '3000000.00', '300000.00', '0.00', '250000.00', '3050000.00', NULL, 'DaThanhToan'),
('HD005', 'DDP005', 5, '2025-12-29', 'Chuyển khoản', '2025-12-29', '1200000.00', '0.00', '50000.00', '0.00', '1250000.00', 'Hoàn tiền đặt cọc', 'DaHuy');

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `idKH` int(11) NOT NULL auto_increment,
  `hoTen` varchar(100) collate utf8_unicode_ci NOT NULL,
  `email` varchar(100) collate utf8_unicode_ci default NULL,
  `matKhau` varchar(255) collate utf8_unicode_ci default NULL,
  `soDienThoai` varchar(20) collate utf8_unicode_ci default NULL,
  `CCCD` varchar(20) collate utf8_unicode_ci default NULL,
  `diaChi` varchar(200) collate utf8_unicode_ci default NULL,
  `loaiKH` enum('Thuong','VIP') collate utf8_unicode_ci NOT NULL default 'Thuong',
  `vaiTro` varchar(50) collate utf8_unicode_ci default 'KhachHang',
  `trangThai` varchar(50) collate utf8_unicode_ci default 'HoatDong',
  PRIMARY KEY  (`idKH`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=28 ;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`idKH`, `hoTen`, `email`, `matKhau`, `soDienThoai`, `CCCD`, `diaChi`, `loaiKH`, `vaiTro`, `trangThai`) VALUES
(1, 'Nguyễn Văn An', 'an@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0911111111', '111111111111', 'Hà Nội', 'Thuong', 'KhachHang', 'HoatDong'),
(2, 'Trần Thị Bình', 'binh@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0922222222', '222222222222', 'Hải Phòng', 'VIP', 'KhachHang', 'HoatDong'),
(3, 'Lê Văn Cường', 'cuong@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0933333333', '333333333333', 'Đà Nẵng', 'Thuong', 'KhachHang', 'HoatDong'),
(4, 'Phạm Thị Dung', 'dung@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0944444444', '444444444444', 'HCM', 'VIP', 'KhachHang', 'HoatDong'),
(5, 'Hoàng Văn Em', 'em@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0955555555', '555555555555', 'Cần Thơ', 'Thuong', 'KhachHang', 'HoatDong');

-- --------------------------------------------------------

--
-- Table structure for table `khuyenmai`
--

CREATE TABLE `khuyenmai` (
  `maKM` varchar(20) collate utf8_unicode_ci NOT NULL,
  `tenCT` varchar(100) collate utf8_unicode_ci default NULL,
  `mucGiam` decimal(5,2) default NULL,
  `ngayBatDau` date default NULL,
  `ngayKetThuc` date default NULL,
  PRIMARY KEY  (`maKM`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `khuyenmai`
--

INSERT INTO `khuyenmai` (`maKM`, `tenCT`, `mucGiam`, `ngayBatDau`, `ngayKetThuc`) VALUES
('KM001', 'Giảm giá hè 2024', '10.00', '2025-12-01', '2024-12-31'),
('KM002', 'Ưu đãi tuần lễ', '15.00', '2025-11-01', '2025-12-15'),
('KM003', 'Khách hàng VIP', '20.00', '2026-01-01', '2024-01-01'),
('KM004', 'Combo phòng + dịch vụ', '25.00', '2025-12-15', '2026-01-31');

-- --------------------------------------------------------

--
-- Table structure for table `letan`
--

CREATE TABLE `letan` (
  `maNS` varchar(20) collate utf8_unicode_ci NOT NULL,
  `ngoaiNgu` varchar(100) collate utf8_unicode_ci default NULL,
  `kyNang` varchar(200) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`maNS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `letan`
--

INSERT INTO `letan` (`maNS`, `ngoaiNgu`, `kyNang`) VALUES
('NS004', 'Tiếng Anh, Tiếng Nhật', 'Giao tiếp tốt, xử lý nhanh');

-- --------------------------------------------------------

--
-- Table structure for table `loaiphong`
--

CREATE TABLE `loaiphong` (
  `maLoaiPhong` varchar(20) collate utf8_unicode_ci NOT NULL,
  `tenLoaiPhong` varchar(100) collate utf8_unicode_ci default NULL,
  `giaCoBan` decimal(10,2) default NULL,
  `moTa` varchar(200) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`maLoaiPhong`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `loaiphong`
--

INSERT INTO `loaiphong` (`maLoaiPhong`, `tenLoaiPhong`, `giaCoBan`, `moTa`) VALUES
('LP001', 'Phòng Standard', '500000.00', 'Phòng đơn cơ bản'),
('LP002', 'Phòng Superior', '800000.00', 'Phòng đôi view biển'),
('LP003', 'Phòng Deluxe', '1200000.00', 'Phòng cao cấp'),
('LP004', 'Phòng Suite', '2000000.00', 'Phòng thương gia'),
('LP005', 'Phòng Family', '1500000.00', 'Phòng gia đình 4 người');

-- --------------------------------------------------------

--
-- Table structure for table `nhansu`
--

CREATE TABLE `nhansu` (
  `maNS` varchar(20) collate utf8_unicode_ci NOT NULL,
  `idUser` int(11) default NULL,
  `hoTen` varchar(100) collate utf8_unicode_ci default NULL,
  `gioiTinh` varchar(10) collate utf8_unicode_ci default NULL,
  `soDienThoai` varchar(20) collate utf8_unicode_ci default NULL,
  `ngayVaoLam` date default NULL,
  PRIMARY KEY  (`maNS`),
  KEY `idUser` (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `nhansu`
--

INSERT INTO `nhansu` (`maNS`, `idUser`, `hoTen`, `gioiTinh`, `soDienThoai`, `ngayVaoLam`) VALUES
('NS001', 6, 'Nguyễn Văn Quản Trị', 'Nam', '0910000001', '2024-01-01'),
('NS002', 7, 'Trần Thị Quản Lý', 'Nữ', '0910000002', '2024-01-02'),
('NS004', 8, 'Phạm Thị Lễ Tân', 'Nữ', '0910000004', '2024-01-04'),
('NS005', 9, 'Hoàng Văn Buồng Phòng', 'Nam', '0910000005', '2024-01-05');

-- --------------------------------------------------------

--
-- Table structure for table `nhanvienbuongphong`
--

CREATE TABLE `nhanvienbuongphong` (
  `maNS` varchar(20) collate utf8_unicode_ci NOT NULL,
  `khuVucPhuTrach` varchar(100) collate utf8_unicode_ci default NULL,
  `phuCap` decimal(10,2) default NULL,
  PRIMARY KEY  (`maNS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `nhanvienbuongphong`
--

INSERT INTO `nhanvienbuongphong` (`maNS`, `khuVucPhuTrach`, `phuCap`) VALUES
('NS005', 'Tầng 1-5', '500000.00');

-- --------------------------------------------------------

--
-- Table structure for table `phong`
--

CREATE TABLE `phong` (
  `maPhong` varchar(20) collate utf8_unicode_ci NOT NULL,
  `maLoaiPhong` varchar(20) collate utf8_unicode_ci default NULL,
  `tinhTrang` varchar(50) collate utf8_unicode_ci default NULL,
  `giaPhong` decimal(10,2) default NULL,
  `sucChua` int(11) default NULL,
  `tangPhong` varchar(20) collate utf8_unicode_ci default NULL,
  `soPhong` varchar(10) collate utf8_unicode_ci default NULL,
  `hangPhong` varchar(50) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`maPhong`),
  KEY `maLoaiPhong` (`maLoaiPhong`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `phong`
--

INSERT INTO `phong` (`maPhong`, `maLoaiPhong`, `tinhTrang`, `giaPhong`, `sucChua`, `tangPhong`, `soPhong`, `hangPhong`) VALUES
('P101', 'LP001', 'Trống', '500000.00', 2, '1', '101', 'Standard'),
('P201', 'LP002', 'Đã đặt', '800000.00', 2, '2', '201', 'Superior'),
('P301', 'LP003', 'Trống', '1200000.00', 2, '3', '301', 'Deluxe'),
('P401', 'LP004', 'Trống', '2000000.00', 2, '4', '401', 'Suite'),
('P501', 'LP005', 'Đang ở', '1500000.00', 4, '5', '501', 'Family');

-- --------------------------------------------------------

--
-- Table structure for table `taikhoan`
--

CREATE TABLE `taikhoan` (
  `idUser` int(11) NOT NULL auto_increment,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  `matKhau` varchar(255) collate utf8_unicode_ci NOT NULL,
  `loaiTaiKhoan` enum('KhachHang','Doan','NhanVien','Admin') collate utf8_unicode_ci NOT NULL default 'KhachHang',
  `idThamChieu` int(11) default NULL,
  `vaiTro` varchar(50) collate utf8_unicode_ci default NULL,
  `trangThai` varchar(50) collate utf8_unicode_ci default 'HoatDong',
  `ngayTao` date default NULL,
  PRIMARY KEY  (`idUser`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=47 ;

--
-- Dumping data for table `taikhoan`
--

INSERT INTO `taikhoan` (`idUser`, `email`, `matKhau`, `loaiTaiKhoan`, `idThamChieu`, `vaiTro`, `trangThai`, `ngayTao`) VALUES
(1, 'an@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 1, 'KhachHang', 'HoatDong', '2024-01-01'),
(2, 'binh@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 2, 'KhachHang', 'HoatDong', '2024-01-02'),
(3, 'cuong@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 3, 'KhachHang', 'HoatDong', '2024-01-03'),
(4, 'dung@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 4, 'KhachHang', 'HoatDong', '2024-01-04'),
(5, 'em@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 5, 'KhachHang', 'HoatDong', '2024-01-05'),
(6, 'quantri@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'Admin', NULL, 'admin', 'HoatDong', '2024-01-01'),
(7, 'quanly@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'NhanVien', NULL, 'quanly', 'HoatDong', '2024-01-02'),
(8, 'letan@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'NhanVien', NULL, 'letan', 'HoatDong', '2024-01-04'),
(9, 'buongphong@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'NhanVien', NULL, 'buongphong', 'HoatDong', '2024-01-05');

-- --------------------------------------------------------

--
-- Table structure for table `thanhviendoan`
--

CREATE TABLE `thanhviendoan` (
  `idThanhVien` int(11) NOT NULL auto_increment,
  `idKH` int(11) default NULL,
  `maDoan` varchar(20) collate utf8_unicode_ci default NULL,
  `vaiTro` varchar(50) collate utf8_unicode_ci default 'thanhvien',
  PRIMARY KEY  (`idThanhVien`),
  KEY `idKH` (`idKH`),
  KEY `maDoan` (`maDoan`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `thanhviendoan`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `boithuong`
--
ALTER TABLE `boithuong`
  ADD CONSTRAINT `boithuong_ibfk_1` FOREIGN KEY (`maPhong`) REFERENCES `phong` (`maPhong`);

--
-- Constraints for table `chitietboithuong`
--
ALTER TABLE `chitietboithuong`
  ADD CONSTRAINT `chitietboithuong_ibfk_1` FOREIGN KEY (`maBT`) REFERENCES `boithuong` (`maBT`);

--
-- Constraints for table `chitietdatphong`
--
ALTER TABLE `chitietdatphong`
  ADD CONSTRAINT `chitietdatphong_ibfk_1` FOREIGN KEY (`maDDP`) REFERENCES `dondatphong` (`maDDP`),
  ADD CONSTRAINT `chitietdatphong_ibfk_2` FOREIGN KEY (`maPhong`) REFERENCES `phong` (`maPhong`);

--
-- Constraints for table `dondatphong`
--
ALTER TABLE `dondatphong`
  ADD CONSTRAINT `dondatphong_ibfk_1` FOREIGN KEY (`idKH`) REFERENCES `khachhang` (`idKH`),
  ADD CONSTRAINT `dondatphong_ibfk_2` FOREIGN KEY (`maKM`) REFERENCES `khuyenmai` (`maKM`),
  ADD CONSTRAINT `fk_ddp_phong` FOREIGN KEY (`maPhong`) REFERENCES `phong` (`maPhong`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ddp_user` FOREIGN KEY (`idUser`) REFERENCES `taikhoan` (`idUser`) ON DELETE SET NULL;

--
-- Constraints for table `hd_dichvu`
--
ALTER TABLE `hd_dichvu`
  ADD CONSTRAINT `hd_dichvu_ibfk_1` FOREIGN KEY (`maHD`) REFERENCES `hoadon` (`maHD`) ON DELETE CASCADE,
  ADD CONSTRAINT `hd_dichvu_ibfk_2` FOREIGN KEY (`maDV`) REFERENCES `dichvu` (`maDV`);

--
-- Constraints for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`maDDP`) REFERENCES `dondatphong` (`maDDP`),
  ADD CONSTRAINT `hoadon_ibfk_2` FOREIGN KEY (`idKH`) REFERENCES `khachhang` (`idKH`);

--
-- Constraints for table `letan`
--
ALTER TABLE `letan`
  ADD CONSTRAINT `letan_ibfk_1` FOREIGN KEY (`maNS`) REFERENCES `nhansu` (`maNS`);

--
-- Constraints for table `nhansu`
--
ALTER TABLE `nhansu`
  ADD CONSTRAINT `nhansu_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `taikhoan` (`idUser`);

--
-- Constraints for table `nhanvienbuongphong`
--
ALTER TABLE `nhanvienbuongphong`
  ADD CONSTRAINT `nhanvienbuongphong_ibfk_1` FOREIGN KEY (`maNS`) REFERENCES `nhansu` (`maNS`);

--
-- Constraints for table `phong`
--
ALTER TABLE `phong`
  ADD CONSTRAINT `phong_ibfk_1` FOREIGN KEY (`maLoaiPhong`) REFERENCES `loaiphong` (`maLoaiPhong`);

--
-- Constraints for table `thanhviendoan`
--
ALTER TABLE `thanhviendoan`
  ADD CONSTRAINT `thanhviendoan_ibfk_1` FOREIGN KEY (`idKH`) REFERENCES `khachhang` (`idKH`),
  ADD CONSTRAINT `thanhviendoan_ibfk_2` FOREIGN KEY (`maDoan`) REFERENCES `doan` (`maDoan`);
