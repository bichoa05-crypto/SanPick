-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 01, 2026 at 02:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `datlichthethao`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `auto_update_booking_status` ()   BEGIN
    -- Đơn chờ xác nhận quá giờ -> hủy
    UPDATE dat_san ds
    JOIN khung_gio kg 
        ON ds.khung_gio_id = kg.khung_gio_id
    SET ds.trang_thai = 'da_huy'
    WHERE ds.trang_thai = 'cho_xac_nhan'
      AND TIMESTAMP(ds.ngay_dat, kg.gio_ket_thuc) < NOW();

    -- Đơn đã xác nhận quá giờ -> hoàn thành
    UPDATE dat_san ds
    JOIN khung_gio kg 
        ON ds.khung_gio_id = kg.khung_gio_id
    SET ds.trang_thai = 'hoan_thanh'
    WHERE ds.trang_thai = 'da_xac_nhan'
      AND TIMESTAMP(ds.ngay_dat, kg.gio_ket_thuc) < NOW();
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_dich_vu`
--

CREATE TABLE `chi_tiet_dich_vu` (
  `chi_tiet_id` int(11) NOT NULL,
  `dat_san_id` int(11) NOT NULL,
  `dich_vu_id` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 1,
  `thanh_tien` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chi_tiet_dich_vu`
--

INSERT INTO `chi_tiet_dich_vu` (`chi_tiet_id`, `dat_san_id`, `dich_vu_id`, `so_luong`, `thanh_tien`) VALUES
(24, 37, 7, 2, 550000.00),
(25, 37, 5, 2, 640000.00),
(26, 38, 7, 1, 275000.00),
(27, 38, 5, 1, 320000.00),
(28, 38, 6, 1, 500000.00),
(29, 40, 2, 2, 300000.00),
(30, 40, 7, 2, 550000.00),
(31, 41, 2, 2, 300000.00);

-- --------------------------------------------------------

--
-- Table structure for table `co_so`
--

CREATE TABLE `co_so` (
  `co_so_id` int(11) NOT NULL,
  `ten_co_so` varchar(100) NOT NULL,
  `dia_chi` text NOT NULL,
  `anh_bia` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `co_so`
--

INSERT INTO `co_so` (`co_so_id`, `ten_co_so`, `dia_chi`, `anh_bia`) VALUES
(1, 'Sân PickleBall UTT', '54 P. Triều Khúc, Thanh Xuân Nam, Thanh Liệt, Hà Nội', 'assets/img/cs_1775995746.jpg'),
(2, 'Sân PickleBall UTT - Phú Thọ', 'Số 278 Lam Sơn, phường Vĩnh Yên, Phú Thọ', 'assets/img/cs_1777639568.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `dat_san`
--

CREATE TABLE `dat_san` (
  `dat_san_id` int(11) NOT NULL,
  `khach_hang_id` int(11) NOT NULL,
  `san_id` int(11) NOT NULL,
  `khung_gio_id` int(11) NOT NULL,
  `ngay_dat` date NOT NULL,
  `tien_san` decimal(10,2) NOT NULL,
  `tien_dich_vu` decimal(10,2) DEFAULT 0.00,
  `tong_hoa_don` decimal(10,2) NOT NULL,
  `trang_thai` enum('cho_xac_nhan','da_xac_nhan','hoan_thanh','da_huy') DEFAULT 'cho_xac_nhan',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dat_san`
--

INSERT INTO `dat_san` (`dat_san_id`, `khach_hang_id`, `san_id`, `khung_gio_id`, `ngay_dat`, `tien_san`, `tien_dich_vu`, `tong_hoa_don`, `trang_thai`, `ngay_tao`) VALUES
(36, 14, 9, 18, '2026-04-25', 90000.00, 0.00, 90000.00, 'da_xac_nhan', '2026-04-22 07:37:33'),
(37, 14, 9, 22, '2026-05-02', 160000.00, 1190000.00, 1350000.00, 'da_xac_nhan', '2026-05-01 12:49:41'),
(38, 14, 18, 639, '2026-05-01', 140000.00, 1095000.00, 1235000.00, 'da_xac_nhan', '2026-05-01 12:49:59'),
(39, 16, 9, 23, '2026-05-01', 140000.00, 0.00, 140000.00, 'da_xac_nhan', '2026-05-01 12:53:05'),
(40, 15, 1, 4, '2026-05-01', 150000.00, 850000.00, 1000000.00, 'da_xac_nhan', '2026-05-01 12:55:39'),
(41, 15, 9, 23, '2026-05-06', 140000.00, 300000.00, 440000.00, 'da_xac_nhan', '2026-05-01 12:55:57');

-- --------------------------------------------------------

--
-- Table structure for table `dich_vu`
--

CREATE TABLE `dich_vu` (
  `dich_vu_id` int(11) NOT NULL,
  `ten_dich_vu` varchar(100) NOT NULL,
  `loai_dich_vu` enum('thue_vot','mua_bong','khac') NOT NULL,
  `don_gia` decimal(10,2) NOT NULL,
  `don_vi` varchar(50) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dich_vu`
--

INSERT INTO `dich_vu` (`dich_vu_id`, `ten_dich_vu`, `loai_dich_vu`, `don_gia`, `don_vi`, `mo_ta`) VALUES
(1, 'Head Radical Pro', 'thue_vot', 30000.00, 'Cái', ''),
(2, 'Bóng Dura Outdoor', 'mua_bong', 150000.00, 'Hộp', 'Hộp 6 quả'),
(4, 'Grip PickleBall Pro', 'khac', 15000.00, 'Cái', ''),
(5, 'Bóng Onix Fuse', 'mua_bong', 320000.00, 'Hộp', 'Hộp 6 quả'),
(6, 'Grip PickleBall Comfort', 'khac', 500000.00, 'Cái', ''),
(7, 'Bóng Franklin X-40', 'mua_bong', 275000.00, 'Hộp', 'Hộp 6 quả'),
(8, 'Paddletek Pro', 'thue_vot', 30000.00, 'Cái', ''),
(9, 'Onix Graphite Z5', 'thue_vot', 40000.00, 'Cái', ''),
(10, 'Wilson Pro Staff', 'thue_vot', 50000.00, 'Cái', '');

-- --------------------------------------------------------

--
-- Table structure for table `hinh_anh_co_so`
--

CREATE TABLE `hinh_anh_co_so` (
  `hinh_id` int(11) NOT NULL,
  `co_so_id` int(11) NOT NULL,
  `duong_dan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hinh_anh_co_so`
--

INSERT INTO `hinh_anh_co_so` (`hinh_id`, `co_so_id`, `duong_dan`) VALUES
(18, 1, 'assets/img/cs1_1777639541_809.jpg'),
(19, 1, 'assets/img/cs1_1777639541_443.png'),
(20, 1, 'assets/img/cs1_1777639541_771.jpg'),
(21, 1, 'assets/img/cs1_1777639541_224.jpg'),
(22, 2, 'assets/img/cs2_1777639583_878.png'),
(23, 2, 'assets/img/cs2_1777639583_276.jpg'),
(24, 2, 'assets/img/cs2_1777639583_254.jpg'),
(25, 2, 'assets/img/cs2_1777639583_938.png'),
(26, 2, 'assets/img/cs2_1777639583_383.jpg'),
(27, 2, 'assets/img/cs2_1777639583_700.png');

-- --------------------------------------------------------

--
-- Table structure for table `khung_gio`
--

CREATE TABLE `khung_gio` (
  `khung_gio_id` int(11) NOT NULL,
  `san_id` int(11) NOT NULL,
  `gio_bat_dau` time NOT NULL,
  `gio_ket_thuc` time NOT NULL,
  `gia` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khung_gio`
--

INSERT INTO `khung_gio` (`khung_gio_id`, `san_id`, `gio_bat_dau`, `gio_ket_thuc`, `gia`) VALUES
(1, 1, '06:00:00', '08:00:00', 80000.00),
(2, 1, '08:00:00', '10:00:00', 80000.00),
(3, 1, '18:00:00', '20:00:00', 120000.00),
(4, 1, '20:00:00', '22:00:00', 150000.00),
(5, 2, '18:00:00', '20:00:00', 120000.00),
(6, 2, '20:00:00', '22:00:00', 150000.00),
(8, 4, '20:00:00', '22:00:00', 240000.00),
(9, 1, '10:00:00', '12:00:00', 100000.00),
(11, 1, '12:00:00', '14:00:00', 100000.00),
(12, 1, '22:00:00', '23:00:00', 60000.00),
(13, 9, '00:00:00', '02:00:00', 100000.00),
(14, 9, '02:00:00', '04:00:00', 100000.00),
(15, 9, '04:00:00', '06:00:00', 100000.00),
(16, 9, '06:00:00', '08:00:00', 120000.00),
(17, 9, '08:00:00', '10:00:00', 120000.00),
(18, 9, '10:00:00', '11:30:00', 90000.00),
(19, 9, '13:00:00', '15:00:00', 120000.00),
(20, 9, '15:00:00', '17:00:00', 120000.00),
(21, 9, '17:00:00', '19:00:00', 160000.00),
(22, 9, '19:00:00', '21:00:00', 160000.00),
(23, 9, '21:00:00', '23:00:00', 140000.00),
(519, 6, '00:00:00', '02:00:00', 100000.00),
(520, 6, '02:00:00', '04:00:00', 100000.00),
(521, 6, '04:00:00', '06:00:00', 100000.00),
(522, 6, '06:00:00', '08:00:00', 120000.00),
(523, 6, '08:00:00', '10:00:00', 120000.00),
(524, 6, '10:00:00', '11:30:00', 90000.00),
(525, 6, '13:00:00', '15:00:00', 120000.00),
(526, 6, '15:00:00', '17:00:00', 120000.00),
(527, 6, '17:00:00', '19:00:00', 160000.00),
(528, 6, '19:00:00', '21:00:00', 160000.00),
(529, 6, '21:00:00', '23:00:00', 140000.00),
(530, 8, '00:00:00', '02:00:00', 100000.00),
(531, 8, '02:00:00', '04:00:00', 100000.00),
(532, 8, '04:00:00', '06:00:00', 100000.00),
(533, 8, '06:00:00', '08:00:00', 120000.00),
(534, 8, '08:00:00', '10:00:00', 120000.00),
(535, 8, '10:00:00', '11:30:00', 90000.00),
(536, 8, '13:00:00', '15:00:00', 120000.00),
(537, 8, '15:00:00', '17:00:00', 120000.00),
(538, 8, '17:00:00', '19:00:00', 160000.00),
(539, 8, '19:00:00', '21:00:00', 160000.00),
(540, 8, '21:00:00', '23:00:00', 140000.00),
(541, 10, '00:00:00', '02:00:00', 100000.00),
(542, 10, '02:00:00', '04:00:00', 100000.00),
(543, 10, '04:00:00', '06:00:00', 100000.00),
(544, 10, '06:00:00', '08:00:00', 120000.00),
(545, 10, '08:00:00', '10:00:00', 120000.00),
(546, 10, '10:00:00', '11:30:00', 90000.00),
(547, 10, '13:00:00', '15:00:00', 120000.00),
(548, 10, '15:00:00', '17:00:00', 120000.00),
(549, 10, '17:00:00', '19:00:00', 160000.00),
(550, 10, '19:00:00', '21:00:00', 160000.00),
(551, 10, '21:00:00', '23:00:00', 140000.00),
(563, 12, '00:00:00', '02:00:00', 100000.00),
(564, 12, '02:00:00', '04:00:00', 100000.00),
(565, 12, '04:00:00', '06:00:00', 100000.00),
(566, 12, '06:00:00', '08:00:00', 120000.00),
(567, 12, '08:00:00', '10:00:00', 120000.00),
(568, 12, '10:00:00', '11:30:00', 90000.00),
(569, 12, '13:00:00', '15:00:00', 120000.00),
(570, 12, '15:00:00', '17:00:00', 120000.00),
(571, 12, '17:00:00', '19:00:00', 160000.00),
(572, 12, '19:00:00', '21:00:00', 160000.00),
(573, 12, '21:00:00', '23:00:00', 140000.00),
(596, 15, '00:00:00', '02:00:00', 100000.00),
(597, 15, '02:00:00', '04:00:00', 100000.00),
(598, 15, '04:00:00', '06:00:00', 100000.00),
(599, 15, '06:00:00', '08:00:00', 120000.00),
(600, 15, '08:00:00', '10:00:00', 120000.00),
(601, 15, '10:00:00', '11:30:00', 90000.00),
(602, 15, '13:00:00', '15:00:00', 120000.00),
(603, 15, '15:00:00', '17:00:00', 120000.00),
(604, 15, '17:00:00', '19:00:00', 160000.00),
(605, 15, '19:00:00', '21:00:00', 160000.00),
(606, 15, '21:00:00', '23:00:00', 140000.00),
(607, 16, '00:00:00', '02:00:00', 100000.00),
(608, 16, '02:00:00', '04:00:00', 100000.00),
(609, 16, '04:00:00', '06:00:00', 100000.00),
(610, 16, '06:00:00', '08:00:00', 120000.00),
(611, 16, '08:00:00', '10:00:00', 120000.00),
(612, 16, '10:00:00', '11:30:00', 90000.00),
(613, 16, '13:00:00', '15:00:00', 120000.00),
(614, 16, '15:00:00', '17:00:00', 120000.00),
(615, 16, '17:00:00', '19:00:00', 160000.00),
(616, 16, '19:00:00', '21:00:00', 160000.00),
(617, 16, '21:00:00', '23:00:00', 140000.00),
(629, 18, '00:00:00', '02:00:00', 100000.00),
(630, 18, '02:00:00', '04:00:00', 100000.00),
(631, 18, '04:00:00', '06:00:00', 100000.00),
(632, 18, '06:00:00', '08:00:00', 120000.00),
(633, 18, '08:00:00', '10:00:00', 120000.00),
(634, 18, '10:00:00', '11:30:00', 90000.00),
(635, 18, '13:00:00', '15:00:00', 120000.00),
(636, 18, '15:00:00', '17:00:00', 120000.00),
(637, 18, '17:00:00', '19:00:00', 160000.00),
(638, 18, '19:00:00', '21:00:00', 160000.00),
(639, 18, '21:00:00', '23:00:00', 140000.00),
(640, 19, '00:00:00', '02:00:00', 100000.00),
(641, 19, '02:00:00', '04:00:00', 100000.00),
(642, 19, '04:00:00', '06:00:00', 100000.00),
(643, 19, '06:00:00', '08:00:00', 120000.00),
(644, 19, '08:00:00', '10:00:00', 120000.00),
(645, 19, '10:00:00', '11:30:00', 90000.00),
(646, 19, '13:00:00', '15:00:00', 120000.00),
(647, 19, '15:00:00', '17:00:00', 120000.00),
(648, 19, '17:00:00', '19:00:00', 160000.00),
(649, 19, '19:00:00', '21:00:00', 160000.00),
(650, 19, '21:00:00', '23:00:00', 140000.00),
(651, 20, '00:00:00', '02:00:00', 100000.00),
(652, 20, '02:00:00', '04:00:00', 100000.00),
(653, 20, '04:00:00', '06:00:00', 100000.00),
(654, 20, '06:00:00', '08:00:00', 120000.00),
(655, 20, '08:00:00', '10:00:00', 120000.00),
(656, 20, '10:00:00', '11:30:00', 90000.00),
(657, 20, '13:00:00', '15:00:00', 120000.00),
(658, 20, '15:00:00', '17:00:00', 120000.00),
(659, 20, '17:00:00', '19:00:00', 160000.00),
(660, 20, '19:00:00', '21:00:00', 160000.00),
(661, 20, '21:00:00', '23:00:00', 140000.00),
(662, 4, '18:00:00', '20:00:00', 240000.00),
(663, 13, '19:00:00', '20:00:00', 100000.00),
(664, 13, '20:00:00', '21:00:00', 100000.00),
(665, 13, '21:00:00', '22:00:00', 100000.00),
(666, 13, '22:00:00', '23:00:00', 100000.00);

-- --------------------------------------------------------

--
-- Table structure for table `khu_vuc`
--

CREATE TABLE `khu_vuc` (
  `khu_vuc_id` int(11) NOT NULL,
  `co_so_id` int(11) NOT NULL,
  `ten_kv` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khu_vuc`
--

INSERT INTO `khu_vuc` (`khu_vuc_id`, `co_so_id`, `ten_kv`) VALUES
(1, 1, 'Ngoài Trời'),
(2, 1, 'Trong Nhà'),
(3, 2, 'Khu VIP'),
(4, 1, 'Mái Che'),
(5, 2, 'Trong Nhà'),
(6, 2, 'Ngoài Trời');

-- --------------------------------------------------------

--
-- Table structure for table `san`
--

CREATE TABLE `san` (
  `san_id` int(11) NOT NULL,
  `khu_vuc_id` int(11) NOT NULL,
  `ten_san` varchar(50) NOT NULL,
  `trang_thai` enum('hoat_dong','bao_tri') DEFAULT 'hoat_dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `san`
--

INSERT INTO `san` (`san_id`, `khu_vuc_id`, `ten_san`, `trang_thai`) VALUES
(1, 1, 'Sân T1', 'hoat_dong'),
(2, 1, 'Sân T2', 'hoat_dong'),
(4, 3, 'Sân VIP 1', 'hoat_dong'),
(6, 5, 'Sân N1', 'hoat_dong'),
(8, 1, 'Test', 'bao_tri'),
(9, 4, 'Sân C1', 'hoat_dong'),
(10, 5, 'Sân N2', 'hoat_dong'),
(12, 6, 'Sân T3', 'hoat_dong'),
(13, 3, 'Sân VIP 2', 'hoat_dong'),
(15, 4, 'Sân C2', 'hoat_dong'),
(16, 4, 'Sân C3', 'bao_tri'),
(18, 2, 'Sân N3', 'hoat_dong'),
(19, 2, 'Sân N4', 'hoat_dong'),
(20, 2, 'Sân N5', 'bao_tri'),
(21, 2, 'Sân N', 'hoat_dong');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `sdt` varchar(15) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'assets/img/default-avatar.png',
  `vai_tro` enum('chu_san','khach_hang') NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `ho_ten`, `email`, `mat_khau`, `sdt`, `avatar`, `vai_tro`, `ngay_tao`) VALUES
(7, 'Chủ Sân', 'admin@gmail.com', '$2y$10$33YXF37D9qt0jGH.Ps/Svet4JfquNG/6dNr6xcKyRmrJYizsbVMCC', '0123456789', 'assets/img/avatar_7_1777639354.png', 'chu_san', '2026-03-16 14:35:30'),
(14, 'Kiều Đức Tài', 'tai1@gmail.com', '$2y$10$MB5wH6.F99T3OazgVmCk0eR4ff9iJxtMpF5h47WwY1HRXitI71eca', '0345616505', 'assets/img/avatar_14_1777639717.png', 'khach_hang', '2026-04-22 07:36:46'),
(15, 'Trần Thanh Tùng', 'tung1@gmail.com', '$2y$10$4WAgEqbvunB8U7sHhjmpv.eyOPHwqk73rAgGgUz7wZQ83k5sLZi6C', '0987654321', 'assets/img/avatar_15_1777640092.png', 'khach_hang', '2026-05-01 12:51:28'),
(16, 'Đỗ Quang Đăng', 'dang1@gmail.com', '$2y$10$DI8dAaUvnnMJko6SAdeTAeWGuyuolZQvxxZlHs8uvlwzO2Z.PVpPu', '0298273273', 'assets/img/avatar_16_1777640032.png', 'khach_hang', '2026-05-01 12:52:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chi_tiet_dich_vu`
--
ALTER TABLE `chi_tiet_dich_vu`
  ADD PRIMARY KEY (`chi_tiet_id`),
  ADD KEY `dat_san_id` (`dat_san_id`),
  ADD KEY `dich_vu_id` (`dich_vu_id`);

--
-- Indexes for table `co_so`
--
ALTER TABLE `co_so`
  ADD PRIMARY KEY (`co_so_id`);

--
-- Indexes for table `dat_san`
--
ALTER TABLE `dat_san`
  ADD PRIMARY KEY (`dat_san_id`),
  ADD KEY `khach_hang_id` (`khach_hang_id`),
  ADD KEY `khung_gio_id` (`khung_gio_id`);

--
-- Indexes for table `dich_vu`
--
ALTER TABLE `dich_vu`
  ADD PRIMARY KEY (`dich_vu_id`);

--
-- Indexes for table `hinh_anh_co_so`
--
ALTER TABLE `hinh_anh_co_so`
  ADD PRIMARY KEY (`hinh_id`),
  ADD KEY `co_so_id` (`co_so_id`);

--
-- Indexes for table `khung_gio`
--
ALTER TABLE `khung_gio`
  ADD PRIMARY KEY (`khung_gio_id`),
  ADD KEY `san_id` (`san_id`);

--
-- Indexes for table `khu_vuc`
--
ALTER TABLE `khu_vuc`
  ADD PRIMARY KEY (`khu_vuc_id`),
  ADD KEY `co_so_id` (`co_so_id`);

--
-- Indexes for table `san`
--
ALTER TABLE `san`
  ADD PRIMARY KEY (`san_id`),
  ADD KEY `khu_vuc_id` (`khu_vuc_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `sdt` (`sdt`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chi_tiet_dich_vu`
--
ALTER TABLE `chi_tiet_dich_vu`
  MODIFY `chi_tiet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `co_so`
--
ALTER TABLE `co_so`
  MODIFY `co_so_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dat_san`
--
ALTER TABLE `dat_san`
  MODIFY `dat_san_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `dich_vu`
--
ALTER TABLE `dich_vu`
  MODIFY `dich_vu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `hinh_anh_co_so`
--
ALTER TABLE `hinh_anh_co_so`
  MODIFY `hinh_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `khung_gio`
--
ALTER TABLE `khung_gio`
  MODIFY `khung_gio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=667;

--
-- AUTO_INCREMENT for table `khu_vuc`
--
ALTER TABLE `khu_vuc`
  MODIFY `khu_vuc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `san`
--
ALTER TABLE `san`
  MODIFY `san_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chi_tiet_dich_vu`
--
ALTER TABLE `chi_tiet_dich_vu`
  ADD CONSTRAINT `chi_tiet_dich_vu_ibfk_1` FOREIGN KEY (`dat_san_id`) REFERENCES `dat_san` (`dat_san_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_dich_vu_ibfk_2` FOREIGN KEY (`dich_vu_id`) REFERENCES `dich_vu` (`dich_vu_id`) ON DELETE CASCADE;

--
-- Constraints for table `dat_san`
--
ALTER TABLE `dat_san`
  ADD CONSTRAINT `dat_san_ibfk_1` FOREIGN KEY (`khach_hang_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `hinh_anh_co_so`
--
ALTER TABLE `hinh_anh_co_so`
  ADD CONSTRAINT `hinh_anh_co_so_ibfk_1` FOREIGN KEY (`co_so_id`) REFERENCES `co_so` (`co_so_id`) ON DELETE CASCADE;

--
-- Constraints for table `khung_gio`
--
ALTER TABLE `khung_gio`
  ADD CONSTRAINT `khung_gio_ibfk_1` FOREIGN KEY (`san_id`) REFERENCES `san` (`san_id`) ON DELETE CASCADE;

--
-- Constraints for table `khu_vuc`
--
ALTER TABLE `khu_vuc`
  ADD CONSTRAINT `khu_vuc_ibfk_1` FOREIGN KEY (`co_so_id`) REFERENCES `co_so` (`co_so_id`) ON DELETE CASCADE;

--
-- Constraints for table `san`
--
ALTER TABLE `san`
  ADD CONSTRAINT `san_ibfk_1` FOREIGN KEY (`khu_vuc_id`) REFERENCES `khu_vuc` (`khu_vuc_id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `ev_auto_update_booking_status` ON SCHEDULE EVERY 1 MINUTE STARTS '2026-04-12 17:02:39' ON COMPLETION NOT PRESERVE ENABLE DO CALL auto_update_booking_status()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
