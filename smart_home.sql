-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th10 17, 2025 lúc 08:48 PM
-- Phiên bản máy phục vụ: 10.11.14-MariaDB-log
-- Phiên bản PHP: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `nrojunec_smart_home`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) DEFAULT NULL,
  `api_key` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `devices`
--

INSERT INTO `devices` (`id`, `device_name`, `api_key`, `created_at`) VALUES
(1, 'my_esp32', '5ef4ec864d436ceb3a6304441c33855b', '2025-10-08 13:42:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rfid_logs`
--

CREATE TABLE `rfid_logs` (
  `id` bigint(20) NOT NULL,
  `uid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(32) NOT NULL,
  `note` text DEFAULT NULL,
  `logged_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `rfid_logs`
--

INSERT INTO `rfid_logs` (`id`, `uid`, `user_id`, `action`, `note`, `logged_at`) VALUES
(1, '1FD195C3', NULL, 'entry', 'from_esp32', '2025-10-10 11:51:42'),
(2, '1FD195C3', NULL, 'entry', 'from_esp32', '2025-10-10 11:52:22'),
(3, '1FD195C3', NULL, 'entry', 'from_esp32', '2025-10-10 11:53:43'),
(4, '1FD195C3', NULL, 'entry', 'from_esp32', '2025-10-10 11:56:02'),
(5, '1FD195C3', NULL, 'entry', 'from_esp32', '2025-10-10 11:56:10'),
(6, '1FD195C3', NULL, 'entry', 'from_esp32', '2025-10-10 11:58:57'),
(7, '1FD195C3', NULL, 'entry', 'from_esp32', '2025-10-10 11:58:58'),
(8, '1FD195C3', 2, 'entry', 'from_esp32', '2025-10-10 11:59:15'),
(9, '1FD195C3', 2, 'entry', 'from_esp32', '2025-10-10 11:59:24'),
(10, 'FB5EF504', NULL, 'entry', 'from_esp32', '2025-10-10 11:59:55'),
(11, '1FD195C3', 2, 'entry', 'from_esp32', '2025-10-10 12:00:03'),
(12, '1FD195C3', 2, 'entry', 'from_esp32', '2025-10-10 12:02:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rfid_users`
--

CREATE TABLE `rfid_users` (
  `id` int(11) NOT NULL,
  `uid` varchar(64) NOT NULL,
  `name` varchar(150) NOT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `rfid_users`
--

INSERT INTO `rfid_users` (`id`, `uid`, `name`, `meta`, `created_at`) VALUES
(2, '1FD195C3', 'D?', NULL, '2025-10-10 04:59:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sensor_data`
--

CREATE TABLE `sensor_data` (
  `id` bigint(20) NOT NULL,
  `device_id` int(11) DEFAULT NULL,
  `temp` float DEFAULT NULL,
  `hum` float DEFAULT NULL,
  `gas` float DEFAULT NULL,
  `ldr` int(11) DEFAULT NULL,
  `rain` float DEFAULT NULL,
  `extra` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extra`)),
  `recorded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `sensor_data`
--

INSERT INTO `sensor_data` (`id`, `device_id`, `temp`, `hum`, `gas`, `ldr`, `rain`, `extra`, `recorded_at`) VALUES
(1, 1, 25.6, 61, 5.5, 4095, NULL, NULL, '2025-10-10 11:04:29'),
(2, 1, 25.6, 61, 5.5, 4095, NULL, NULL, '2025-10-10 11:04:35'),
(3, 1, 25.6, 61, 5.5, 4095, NULL, NULL, '2025-10-10 11:04:40'),
(4, 1, 25.6, 61, 5.5, 4095, NULL, NULL, '2025-10-10 11:04:46'),

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`);

--
-- Chỉ mục cho bảng `rfid_logs`
--
ALTER TABLE `rfid_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `rfid_users`
--
ALTER TABLE `rfid_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Chỉ mục cho bảng `sensor_data`
--
ALTER TABLE `sensor_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `rfid_logs`
--
ALTER TABLE `rfid_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `rfid_users`
--
ALTER TABLE `rfid_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `sensor_data`
--
ALTER TABLE `sensor_data`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=550;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `rfid_logs`
--
ALTER TABLE `rfid_logs`
  ADD CONSTRAINT `rfid_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `rfid_users` (`id`) ON DELETE SET NULL;

--
-- Ràng buộc cho bảng `sensor_data`
--
ALTER TABLE `sensor_data`
  ADD CONSTRAINT `sensor_data_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
