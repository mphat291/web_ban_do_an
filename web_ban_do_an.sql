-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th7 04, 2026 lúc 06:41 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `web_ban_do_an`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(1, 3, 12, 1),
(2, 3, 7, 1),
(12, 5, 12, 8);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Món Ăn Vặt', 'Các món ăn nhẹ, ăn vặt siêu ngon'),
(2, 'Cơm Trưa', 'Cơm trưa văn phòng, no bụng chắc dạ'),
(3, 'Thức Uống', 'Nước giải khát, trà sữa cực đã');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `note` text DEFAULT NULL,
  `total_money` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `fullname`, `phone`, `address`, `note`, `total_money`, `status`, `created_at`) VALUES
(1, 2, 'Quản Trị Viên', '0942398774', 'p8,tp cà mau', '', 25000, 'pending', '2026-07-03 23:46:19'),
(2, 2, 'Quản Trị Viên', '0942398774', 'p8,tp cà mau', '', 50000, 'pending', '2026-07-03 23:46:32'),
(3, 2, 'Quản Trị Viên', '0942398774', 'p8,tp cà mau', 'ngon mới chịu', 35000, 'pending', '2026-07-04 00:24:38'),
(4, 3, 'mphat', '0942398774', 'p8,tp cà mau', '', 30000, 'pending', '2026-07-04 18:46:56'),
(5, NULL, 'mphat', '0942398774', 'p8,tp cà mau', '', 30000, 'pending', '2026-07-04 18:55:03'),
(6, 2, 'Quản Trị Viên', '0942398774', 'p8,tp cà mau', '', 20000, 'pending', '2026-07-04 19:01:59'),
(7, 2, 'Quản Trị Viên', '0942398774', 'p8,tp cà mau', '', 45000, 'pending', '2026-07-04 19:08:11'),
(8, 2, 'Quản Trị Viên', '0942398774', 'p8,tp cà mau', '', 45000, 'pending', '2026-07-04 19:14:30'),
(10, 2, 'Quản Trị Viên', '0942398774', 'p8,tp cà mau', '', 30000, 'pending', '2026-07-04 19:19:02'),
(12, 2, 'Quản Trị Viên', '0942398774', 'p8,tp cà mau', '', 30000, 'pending', '2026-07-04 19:20:43'),
(13, 2, 'Quản Trị Viên', '0942398774', 'p8,tp cà mau', '', 20000, 'pending', '2026-07-04 19:27:52'),
(14, 2, 'mphat', '0942398774', 'p8,tp cà mau', 'ngon mới chịu', 65000, 'pending', '2026-07-04 19:29:40'),
(15, 3, 'mphat', '0942398774', 'p8,tp cà mau', '', 30000, 'pending', '2026-07-04 19:30:12'),
(16, 2, 'mphat', '0942398774', 'p8,tp cà mau', 'ngon', 30000, 'pending', '2026-07-04 19:39:18'),
(17, 4, 'mphat', '0942398774', 'p8,tp cà mau', 'k ngon k chịu', 30000, 'pending', '2026-07-04 19:46:34'),
(18, 2, 'mphat', '0942398774', 'p8,tp cà mau', '', 30000, 'pending', '2026-07-04 20:05:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `price`, `quantity`) VALUES
(1, 1, 2, 25000, 1),
(2, 2, 5, 50000, 1),
(3, 3, 3, 35000, 1),
(4, 4, 12, 30000, 1),
(5, 5, 12, 30000, 1),
(6, 6, 7, 20000, 1),
(7, 7, 4, 45000, 1),
(8, 8, 4, 45000, 1),
(9, 10, 6, 30000, 1),
(10, 12, 12, 30000, 1),
(11, 13, 7, 20000, 1),
(12, 14, 3, 35000, 1),
(13, 14, 12, 30000, 1),
(14, 15, 12, 30000, 1),
(15, 16, 12, 30000, 1),
(16, 17, 12, 30000, 1),
(17, 18, 12, 30000, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `image`, `description`, `created_at`) VALUES
(2, 1, 'Bánh Tráng Trộn Sợi Khô Bò', 25000, 'banh_trang.jpg', 'Bánh tráng trộn muối tôm Tây Ninh, trứng cút, khô bò sợi và rau răm thơm ngon.', '2026-07-03 16:04:52'),
(3, 1, 'Cá Viên Chiên Nước Mắm', 35000, 'ca_vien.jpg', 'Cá viên, bò viên chiên ngập dầu, sốt nước mắm tỏi ớt đậm đà, ăn kèm dưa leo.', '2026-07-03 16:04:52'),
(4, 2, 'Cơm Tấm Sườn Bì Chả', 45000, 'com_tam.jpg', 'Cơm tấm dẻo thơm, sườn nướng mật ong thơm phức kết hợp bì chả truyền thống.', '2026-07-03 16:04:52'),
(5, 2, 'Cơm Chiên Hải Sản Cà Mau', 50000, 'com_chien.jpg', 'Cơm chiên hạt cơm vàng giòn, tôm mực tươi ngon roi rói đánh bắt tại vùng biển Cà Mau.', '2026-07-03 16:04:52'),
(6, 3, 'Trà Sữa Trân Châu Đường Đen', 30000, 'tra_sua.jpg', 'Trà sữa đậm vị trà, béo ngậy vị sữa cùng trân châu đen dai giòn sần sật.', '2026-07-03 16:04:52'),
(7, 3, 'Nước Ép Cam Nguyên Chất', 20000, 'nuoc_ep.jpg', 'Nước ép từ những quả cam sành mọng nước, giàu vitamin C giải nhiệt cực tốt.', '2026-07-03 16:04:52'),
(12, 3, 'Matchalatte', 30000, '1783097487_6a47e88f7361f.jpg', 'Sự kết hợp hoàn hảo giữa bột matcha Nhật Bản thượng hạng và sữa tươi béo ngậy.', '2026-07-03 16:51:27'),
(13, 3, 'Cà phê sữa', 15000, '1783098304_6a47ebc00ab2d.jpg', 'Cà phê phin đậm đà phong vị truyền thống, hòa quyện cùng sữa đặc ngọt ngào đầy năng lượng.', '2026-07-03 17:05:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'client',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `role`, `created_at`) VALUES
(2, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'Quản Trị Viên', 'admin', '2026-06-29 02:57:02'),
(3, 'root', '0052069db1a0017f6a27f27e6dcbb919', 'mphat', 'user', '2026-07-04 11:35:51'),
(4, 'mphat', '508df4cb2f4d8f80519256258cfb975f', 'mphat', 'user', '2026-07-04 12:45:56'),
(5, 'khanhduy', '3be101ebdc83e077a8146e60b849fbd4', 'khanhduy', 'user', '2026-07-04 14:12:51');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
