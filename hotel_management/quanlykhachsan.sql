-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 20, 2025 at 07:46 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `quanlykhachsan`
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
('DDP008', 'P101'),
('DDP0011', 'P102'),
('DDP005', 'P102'),
('DDP002', 'P201'),
('DDP010', 'P201'),
('DDP002', 'P202'),
('DDP003', 'P301'),
('DDP006', 'P302'),
('DDP004', 'P401'),
('DDP009', 'P401'),
('DDP007', 'P501');

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
  PRIMARY KEY  (`maDV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dichvu`
--

INSERT INTO `dichvu` (`maDV`, `tenDV`, `loaiDV`, `donGia`, `trangThai`, `moTa`) VALUES
('DV001', 'Buffet sáng', 'Ăn uống', '200000.00', 'HoatDong', 'Buffet sáng quốc tế'),
('DV002', 'Massage', 'Spa', '500000.00', 'HoatDong', 'Massage toàn thân'),
('DV003', 'Xe đưa đón', 'Vận chuyển', '300000.00', 'HoatDong', 'Xe 7 chỗ'),
('DV004', 'Tour du lịch', 'Giải trí', '1000000.00', 'HoatDong', 'Tour đảo Phú Quốc'),
('DV005', 'Giặt ủi', 'Tiện ích', '100000.00', 'HoatDong', 'Giặt ủi nhanh');

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
('D6485', 'Funteam', 'funteam673@doan.com', '350170bbe30d4b55e73056a6cbdf3274', 1, '12345678', 'Doan', 'HoatDong'),
('D9169', 'Funteam', 'funteam610@doan.com', '85a8d335b6de769f6630aa0ae24eef12', 1, '12345678', 'Doan', 'HoatDong'),
('DOAN20251201202128', 'D8049', 'Fun', '9037a9347034659e596b101c2cb7a33d', 1, '0987654321', 'Doan', 'HoatDong');

-- --------------------------------------------------------

--
-- Table structure for table `dondatphong`
--

CREATE TABLE `dondatphong` (
  `maDDP` varchar(20) collate utf8_unicode_ci NOT NULL,
  `idKH` int(11) default NULL,
  `maKM` varchar(20) collate utf8_unicode_ci default NULL,
  `ngayDatPhong` date default NULL,
  `ngayNhanPhong` date default NULL,
  `ngayTraPhong` date default NULL,
  `soLuong` int(11) default NULL,
  `ghiChu` varchar(200) collate utf8_unicode_ci default NULL,
  `trangThai` varchar(50) collate utf8_unicode_ci default 'DangCho',
  `idNhanVien` int(11) default NULL,
  PRIMARY KEY  (`maDDP`),
  KEY `idKH` (`idKH`),
  KEY `maKM` (`maKM`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dondatphong`
--

INSERT INTO `dondatphong` (`maDDP`, `idKH`, `maKM`, `ngayDatPhong`, `ngayNhanPhong`, `ngayTraPhong`, `soLuong`, `ghiChu`, `trangThai`, `idNhanVien`) VALUES
('DDP001', 1, 'KM001', '2024-01-10', '2024-12-25', '2024-12-27', 1, 'Phòng đơn', 'Đã hủy', NULL),
('DDP0010', 1, 'KM002', '2025-11-30', '2025-12-09', '2025-12-10', 1, 'Phòng đơn [Nhận phòng: 2025-12-08 03:04:49] [Trả phòng: 2025-12-08 03:20:05]', 'DaTra', NULL),
('DDP0011', 1, 'KM002', '2025-12-06', '2025-12-10', '2025-12-12', 1, ' Hủy ngày: 2025-12-08 Hủy ngày: 2025-12-08 Hủy ngày: 2025-12-08 Hủy ngày: 2025-12-08 Hủy ngày: 2025-12-08 Hủy ngày: 2025-12-08', 'Đã hủy', NULL),
('DDP002', 2, 'KM002', '2024-01-11', '2024-12-26', '2024-12-28', 2, 'Phòng đôi', 'DangCho', NULL),
('DDP003', 3, 'KM003', '2024-01-12', '2024-12-27', '2024-12-29', 1, 'Phòng cao cấp', 'DangCho', NULL),
('DDP004', 4, 'KM001', '2024-01-13', '2024-12-28', '2024-12-30', 2, '2 phòng', 'DangCho', NULL),
('DDP005', 5, NULL, '2024-01-14', '2024-12-29', '2024-12-31', 1, 'Không KM', 'DangCho', NULL),
('DDP006', 6, 'KM002', '2024-01-01', '2024-01-15', '2024-01-17', 1, 'Đã qua ngày', 'DangCho', NULL),
('DDP007', 7, 'KM003', '2024-01-02', '2024-01-16', '2024-01-18', 2, 'Đã nhận phòng', 'DaNhan', NULL),
('DDP008', 8, NULL, '2024-01-03', '2024-01-10', '2024-01-12', 1, 'Đã trả phòng', 'DaTra', NULL),
('DDP009', 9, 'KM004', '2024-01-15', '2024-12-30', '2025-01-01', 3, 'Phòng gia đình', 'DangCho', NULL),
('DDP010', 10, 'KM002', '2024-01-16', '2025-01-01', '2025-01-03', 2, 'Tết dương lịch', 'DangCho', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hd_dichvu`
--

CREATE TABLE `hd_dichvu` (
  `maHD` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `maDV` varchar(20) collate utf8_unicode_ci NOT NULL default '',
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
  `ngayLap` date default NULL,
  `phuongThucThanhToan` varchar(100) collate utf8_unicode_ci default NULL,
  `ngayThanhToan` date default NULL,
  `tongTien` decimal(15,2) default NULL,
  `noiDungChuyenKhoan` varchar(200) collate utf8_unicode_ci default NULL,
  `trangThai` varchar(50) collate utf8_unicode_ci default 'ChuaThanhToan',
  PRIMARY KEY  (`maHD`),
  KEY `maDDP` (`maDDP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `hoadon`
--

INSERT INTO `hoadon` (`maHD`, `maDDP`, `ngayLap`, `phuongThucThanhToan`, `ngayThanhToan`, `tongTien`, `noiDungChuyenKhoan`, `trangThai`) VALUES
('HD001', 'DDP001', '2024-01-10', 'Tiền mặt', '2024-01-10', '1500000.00', NULL, 'Đã hủy'),
('HD002', 'DDP002', '2024-01-11', 'Chuyển khoản', '2024-01-11', '3200000.00', NULL, 'DaThanhToan'),
('HD003', 'DDP003', '2024-01-12', 'Thẻ tín dụng', NULL, '1200000.00', NULL, 'ChuaThanhToan'),
('HD004', 'DDP004', '2024-01-13', 'Tiền mặt', '2024-01-13', '4000000.00', NULL, 'DaThanhToan'),
('HD005', 'DDP005', '2024-01-14', 'Chuyển khoản', NULL, '500000.00', NULL, 'ChuaThanhToan'),
('HD20251208032005339', 'DDP0010', '2025-12-08', 'Tiền mặt', '2025-12-08', '0.00', '', 'DaThanhToan');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`idKH`, `hoTen`, `email`, `matKhau`, `soDienThoai`, `CCCD`, `diaChi`, `loaiKH`, `vaiTro`, `trangThai`) VALUES
(1, 'Nguyễn Văn An', 'an@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0911111111', '111111111111', 'Hà Nội', 'Thuong', 'KhachHang', 'HoatDong'),
(2, 'Trần Thị Bình', 'binh@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0922222222', '222222222222', 'Hải Phòng', 'VIP', 'KhachHang', 'HoatDong'),
(3, 'Lê Văn Cường', 'cuong@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0933333333', '333333333333', 'Đà Nẵng', 'Thuong', 'KhachHang', 'HoatDong'),
(4, 'Phạm Thị Dung', 'dung@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0944444444', '444444444444', 'HCM', 'VIP', 'KhachHang', 'HoatDong'),
(5, 'Hoàng Văn Em', 'em@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0955555555', '555555555555', 'Cần Thơ', 'Thuong', 'KhachHang', 'HoatDong'),
(6, 'Vũ Thị Phương', 'phuong@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0966666666', '666666666666', 'Nha Trang', 'Thuong', 'KhachHang', 'HoatDong'),
(7, 'Đỗ Văn Giang', 'giang@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0977777777', '777777777777', 'Vũng Tàu', 'VIP', 'KhachHang', 'HoatDong'),
(8, 'Nguyễn Thị Hoa', 'hoa@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0988888888', '888888888888', 'Huế', 'Thuong', 'KhachHang', 'HoatDong'),
(9, 'Trần Văn Hùng', 'hung@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0999999999', '999999999999', 'Quy Nhơn', 'VIP', 'KhachHang', 'HoatDong'),
(10, 'Lê Thị Kim', 'kim@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0900000000', '000000000000', 'Phú Quốc', 'Thuong', 'KhachHang', 'HoatDong'),
(11, 'trannguyen', 'nvquetrann@gmail.com', 'b47d1afa96b5c27f22dbb48b42cca71b', NULL, '1234567890', NULL, 'Thuong', 'KhachHang', 'HoatDong'),
(12, 'Trần', 'trannguyen@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0375985459', '123456372891', 'GV', 'Thuong', 'KhachHang', 'HoatDong'),
(13, 'Trần', 'quetrann0305@gmail.com', '96e79218965eb72c92a549dd5a330112', '0375985459', '123456372891', 'GV', 'Thuong', 'KhachHang', 'HoatDong'),
(14, 'Nguyễn Văn A', 'A@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0984455667', '678787867785', '23 Nguyễn Huệ, TP.HCM', 'Thuong', 'KhachHang', 'HoatDong');

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
('KM001', 'Giảm giá hè 2024', '10.00', '2024-06-01', '2024-08-31'),
('KM002', 'Ưu đãi tuần lễ', '15.00', '2024-01-01', '2024-12-31'),
('KM003', 'Khách hàng VIP', '20.00', '2024-01-01', '2024-12-31'),
('KM004', 'Combo phòng + dịch vụ', '25.00', '2024-07-01', '2024-07-31');

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
('NS004', 'Tiếng Anh, Tiếng Nhật', 'Giao tiếp tốt, xử lý nhanh'),
('NS005', 'Tiếng Anh, Tiếng Hàn', 'Tin học văn phòng, giải quyết vấn đề');

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
('NS001', 18, 'Nguyễn Văn Quản Trị', 'Nam', '0910000001', '2025-12-14'),
('NS002', 19, 'Trần Thị Quản Lý', 'Nữ', '0910000002', '2025-12-14'),
('NS003', 20, 'Lê Văn Kế Toán', 'Nam', '0910000003', '2025-12-14'),
('NS004', 21, 'Phạm Thị Lễ Tân', 'Nữ', '0910000004', '2025-12-14'),
('NS005', 22, 'Hoàng Văn Lễ Tân', 'Nam', '0910000005', '2025-12-14'),
('NS006', 23, 'Vũ Thị Buồng Phòng', 'Nữ', '0910000006', '2025-12-14'),
('NS007', 24, 'Đỗ Văn Buồng Phòng', 'Nam', '0910000007', '2025-12-14');

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
('NS006', 'Tầng 1-3', '500000.00'),
('NS007', 'Tầng 4-5', '500000.00');

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
('P102', 'LP001', 'Trống', '500000.00', 2, '1', '102', 'Standard'),
('P201', 'LP002', 'Trống', '800000.00', 2, '2', '201', 'Superior'),
('P202', 'LP002', 'Trống', '800000.00', 2, '2', '202', 'Superior'),
('P301', 'LP003', 'Trống', '1200000.00', 2, '3', '301', 'Deluxe'),
('P302', 'LP003', 'Đã đặt', '1200000.00', 2, '3', '302', 'Deluxe'),
('P401', 'LP004', 'Trống', '2000000.00', 2, '4', '401', 'Suite'),
('P501', 'LP005', 'Trống', '1500000.00', 4, '5', '501', 'Family');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=26 ;

--
-- Dumping data for table `taikhoan`
--

INSERT INTO `taikhoan` (`idUser`, `email`, `matKhau`, `loaiTaiKhoan`, `idThamChieu`, `vaiTro`, `trangThai`, `ngayTao`) VALUES
(1, 'an@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 1, 'KhachHang', 'HoatDong', '2024-01-01'),
(2, 'binh@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 2, 'KhachHang', 'HoatDong', '2024-01-02'),
(3, 'cuong@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 3, 'KhachHang', 'HoatDong', '2024-01-03'),
(4, 'dung@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 4, 'KhachHang', 'HoatDong', '2024-01-04'),
(5, 'em@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 5, 'KhachHang', 'HoatDong', '2024-01-05'),
(6, 'phuong@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 6, 'KhachHang', 'HoatDong', '2024-01-06'),
(7, 'giang@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 7, 'KhachHang', 'HoatDong', '2024-01-07'),
(8, 'hoa@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 8, 'KhachHang', 'HoatDong', '2024-01-08'),
(9, 'hung@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 9, 'KhachHang', 'HoatDong', '2024-01-09'),
(10, 'kim@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'KhachHang', 10, 'KhachHang', 'HoatDong', '2024-01-10'),
(11, 'Fun', '9037a9347034659e596b101c2cb7a33d', 'Doan', 0, 'Doan', 'HoatDong', NULL),
(13, 'funteam673@doan.com', '350170bbe30d4b55e73056a6cbdf3274', 'Doan', 0, 'Doan', 'HoatDong', '2025-12-01'),
(14, 'funteam610@doan.com', '85a8d335b6de769f6630aa0ae24eef12', 'Doan', 0, 'Doan', 'HoatDong', '2025-12-01'),
(15, 'nvquetrann@gmail.com', '137f5529c1e290fc6b3e16505705e9ac', 'KhachHang', 11, 'KhachHang', 'HoatDong', '2025-12-01'),
(16, 'trannguyen@gmail.com', 'dd81e96dcdccafeebb477376c0e9c2f4', 'KhachHang', 12, 'KhachHang', 'HoatDong', '2025-12-01'),
(17, 'quetrann0305@gmail.com', '35b25534332f9b0d85dce2d2b2b8732b', 'KhachHang', 13, 'KhachHang', 'HoatDong', '2025-12-06'),
(18, 'quantri@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'Admin', NULL, 'admin', 'HoatDong', '2025-12-14'),
(19, 'quanly@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'NhanVien', NULL, 'quanly', 'HoatDong', '2025-12-14'),
(20, 'ketoan@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'NhanVien', NULL, 'ketoan', 'HoatDong', '2025-12-14'),
(21, 'letan1@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'NhanVien', NULL, 'letan', 'HoatDong', '2025-12-14'),
(22, 'letan2@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'NhanVien', NULL, 'letan', 'HoatDong', '2025-12-14'),
(23, 'buongphong1@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'NhanVien', NULL, 'buongphong', 'HoatDong', '2025-12-14'),
(24, 'buongphong2@khachsan.com', 'e10adc3949ba59abbe56e057f20f883e', 'NhanVien', NULL, 'buongphong', 'HoatDong', '2025-12-14'),
(25, 'A@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'KhachHang', 14, 'KhachHang', 'HoatDong', '2025-12-20');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `thanhviendoan`
--

INSERT INTO `thanhviendoan` (`idThanhVien`, `idKH`, `maDoan`, `vaiTro`) VALUES
(1, 11, 'D9169', 'thanhvien');

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
  ADD CONSTRAINT `dondatphong_ibfk_2` FOREIGN KEY (`maKM`) REFERENCES `khuyenmai` (`maKM`);

--
-- Constraints for table `hd_dichvu`
--
ALTER TABLE `hd_dichvu`
  ADD CONSTRAINT `hd_dichvu_ibfk_1` FOREIGN KEY (`maHD`) REFERENCES `hoadon` (`maHD`),
  ADD CONSTRAINT `hd_dichvu_ibfk_2` FOREIGN KEY (`maDV`) REFERENCES `dichvu` (`maDV`);

--
-- Constraints for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`maDDP`) REFERENCES `dondatphong` (`maDDP`);

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
