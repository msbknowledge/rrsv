-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 10, 2026 at 06:42 PM
-- Server version: 11.8.9-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u353140863_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_group`
--

CREATE TABLE `chat_group` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_name` varchar(150) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_group`
--

INSERT INTO `chat_group` (`id`, `group_name`, `description`, `created_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Bandhu', 'SAD', 2, 'active', '2026-09-10 16:36:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chat_group_member`
--

CREATE TABLE `chat_group_member` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `member_role` enum('admin','member') NOT NULL DEFAULT 'member',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `joined_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_group_member`
--

INSERT INTO `chat_group_member` (`id`, `group_id`, `user_id`, `member_role`, `status`, `joined_at`) VALUES
(1, 1, 2, 'admin', 'active', '2026-09-10 16:36:16'),
(2, 1, 7, 'member', 'active', '2026-09-10 16:36:37'),
(3, 1, 9, 'member', 'inactive', '2026-09-10 16:36:51'),
(4, 1, 4, 'member', 'active', '2026-09-10 16:40:18'),
(5, 1, 8, 'member', 'active', '2026-09-10 16:40:57');

-- --------------------------------------------------------

--
-- Table structure for table `chat_group_message`
--

CREATE TABLE `chat_group_message` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `message_type` enum('text','image','file') NOT NULL DEFAULT 'text',
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('sent','deleted') NOT NULL DEFAULT 'sent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_group_message`
--

INSERT INTO `chat_group_message` (`id`, `group_id`, `sender_id`, `message`, `message_type`, `attachment`, `created_at`, `status`) VALUES
(1, 1, 2, 'hi', 'text', NULL, '2026-09-10 16:37:08', 'sent'),
(2, 1, 7, 'hi', 'text', NULL, '2026-09-10 16:38:41', 'sent'),
(3, 1, 8, 'hi', 'text', NULL, '2026-09-10 16:42:24', 'sent'),
(4, 1, 2, 'hi', 'text', NULL, '2026-09-10 16:48:20', 'sent'),
(5, 1, 2, 'ok', 'text', NULL, '2026-09-10 18:08:43', 'sent');

-- --------------------------------------------------------

--
-- Table structure for table `chat_group_message_read`
--

CREATE TABLE `chat_group_message_read` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `message_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `read_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_group_message_read`
--

INSERT INTO `chat_group_message_read` (`id`, `group_id`, `message_id`, `user_id`, `read_at`) VALUES
(1, 1, 1, 7, '2026-09-10 16:38:31'),
(6, 1, 2, 2, '2026-09-10 16:38:42'),
(131, 1, 1, 8, '2026-09-10 16:42:17'),
(132, 1, 2, 8, '2026-09-10 16:42:17'),
(145, 1, 3, 2, '2026-09-10 16:42:27'),
(193, 1, 3, 7, '2026-09-10 16:43:02'),
(430, 1, 4, 7, '2026-09-10 16:48:32'),
(456, 1, 4, 8, '2026-09-10 16:49:02'),
(1707, 1, 5, 7, '2026-09-10 18:10:38');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `receiver_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `message_type` enum('text','image','file') NOT NULL DEFAULT 'text',
  `attachment` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('sent','deleted') NOT NULL DEFAULT 'sent',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `receiver_id`, `message`, `message_type`, `attachment`, `is_read`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 7, 'hi', 'text', NULL, 1, 'sent', '2026-09-10 16:29:24', '2026-09-10 16:38:22'),
(2, 2, 1, 'hi', 'text', NULL, 0, 'sent', '2026-09-10 16:29:34', NULL),
(3, 2, 1, 'hello', 'text', NULL, 0, 'sent', '2026-09-10 18:08:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chat_user`
--

CREATE TABLE `chat_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','student','teacher') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_user`
--

INSERT INTO `chat_user` (`id`, `username`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '6655', 'Admin', 'active', '2026-09-10 16:01:24', '2026-09-10 18:32:54'),
(2, 'SP', '1234567', 'teacher', 'active', '2026-09-10 16:01:24', '2026-09-10 18:39:08'),
(3, 'PP', '123456', 'teacher', 'active', '2026-09-10 16:01:24', '2026-09-10 18:31:34'),
(4, 'MSB', '6655', 'teacher', 'active', '2026-09-10 16:01:24', '2026-09-10 18:32:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_group`
--
ALTER TABLE `chat_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `chat_group_member`
--
ALTER TABLE `chat_group_member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_group_user` (`group_id`,`user_id`),
  ADD KEY `idx_group_id` (`group_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `chat_group_message`
--
ALTER TABLE `chat_group_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_group_id` (`group_id`),
  ADD KEY `idx_sender_id` (`sender_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `chat_group_message_read`
--
ALTER TABLE `chat_group_message_read`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_message_user` (`message_id`,`user_id`),
  ADD KEY `idx_group_user` (`group_id`,`user_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`),
  ADD KEY `idx_conversation` (`sender_id`,`receiver_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `chat_user`
--
ALTER TABLE `chat_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_username_role` (`username`,`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_group`
--
ALTER TABLE `chat_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chat_group_member`
--
ALTER TABLE `chat_group_member`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chat_group_message`
--
ALTER TABLE `chat_group_message`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chat_group_message_read`
--
ALTER TABLE `chat_group_message_read`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2231;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chat_user`
--
ALTER TABLE `chat_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
