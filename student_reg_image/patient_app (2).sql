-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2022 at 11:04 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `patient_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_pin`
--

CREATE TABLE `m_pin` (
  `m_pin_id` int(15) NOT NULL,
  `contact_no` bigint(20) NOT NULL,
  `pin` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_pin`
--

INSERT INTO `m_pin` (`m_pin_id`, `contact_no`, `pin`) VALUES
(1, 1234567893, '$2b$10$asaAYAmJLAQDXf.RpKXiW.vTtRF7vjVExRpxgnycr.bLiQu5t6x96'),
(3, 1234567894, '$2b$10$KBferVcVACts5RdWiBQN3e7UMUr8OQ6K9sCnAmmzNSOLKFI/ooCBi'),
(4, 1234567894, '$2b$10$sLqS9YFfV3d1pHaCntgrMufx/nKWb54Hsq2TZ.2SdfTq6Ico8uh.G'),
(5, 1234567895, '$2b$10$/84ndwbkvRjhgWqpSltd3OnsohoatLFQ46YNmpzB1ZRenf1BDdGUq'),
(6, 1234567896, '$2b$10$kASu9GtKZJ6wgJrC1USnY.sASKTgYDA55ucOXTz91cAWRr.Eqrm4S'),
(7, 1234567890, '$2b$10$lYZuCajEXhqoNlbF4rglP.jUvy1HpoGwbKi2PmpxMoh7J9eYYRpmm'),
(8, 1234567892, '$2b$10$zXviy7Gq.NWaCWpN7BjnHuJ3pnHIZfpJ7JFeRTjf49vA5fX4yhwEK'),
(9, 1234567891, '$2b$10$nIUilbW2XA88Jfhl8L3Qoul0oYiXSrVsxT1Dp8XrP7JdKFiLZYXoW');

-- --------------------------------------------------------

--
-- Table structure for table `patient_registration`
--

CREATE TABLE `patient_registration` (
  `app_id` bigint(20) NOT NULL,
  `contact_no` int(20) NOT NULL,
  `FN` varchar(100) DEFAULT NULL,
  `MN` varchar(100) DEFAULT NULL,
  `LN` varchar(100) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `Gender` enum('M','F') NOT NULL DEFAULT 'M',
  `Email` varchar(160) DEFAULT NULL,
  `App_Reg_Date` timestamp NULL DEFAULT NULL,
  `patient_id` varchar(200) NOT NULL,
  `relation` int(1) NOT NULL DEFAULT 1,
  `relation_contact_no` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patient_registration`
--

INSERT INTO `patient_registration` (`app_id`, `contact_no`, `FN`, `MN`, `LN`, `DOB`, `Gender`, `Email`, `App_Reg_Date`, `patient_id`, `relation`, `relation_contact_no`) VALUES
(1, 1234567898, 'sree', 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(2, 1234567898, 'sree1', 'animesh1', 'Dutta1', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(3, 1234567898, 'sree1', 'animesh1', 'Dutta1', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(4, 1234567898, 'sree1', 'animesh1', 'Dutta1', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(5, 1234567898, 'sree1', 'animesh1', 'Dutta1', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(6, 1234567898, 'sree1', 'animesh1', 'Dutta1', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(7, 1234567898, 'sree1', 'animesh1', 'Dutta1', '2022-09-08', 'M', 'ddddddddddddddddddd@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(8, 2147483647, 'mmdd', 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(9, 2147483647, 'mmdd', 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(10, 2147483647, 'mmdd', 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(11, 2147483647, 'mmdd', 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(12, 2147483647, 'mmdd', 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(13, 2147483647, 'mmdd', 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(14, 2147483647, '', 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(15, 2147483647, NULL, 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(16, 2147483647, '', 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(17, 2147483647, NULL, 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(18, 2147483647, NULL, 'animesh', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891),
(19, 231456789, NULL, 'animesh2', 'Dutta', '2022-09-08', 'M', 'mm@gg.com', '2022-09-01 18:30:00', 'P0001', 1, 1234567891);

-- --------------------------------------------------------

--
-- Table structure for table `relation_master`
--

CREATE TABLE `relation_master` (
  `relation_id` int(11) NOT NULL,
  `relation_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `relation_master`
--

INSERT INTO `relation_master` (`relation_id`, `relation_name`) VALUES
(1, 'Self'),
(2, 'Mother'),
(3, 'Father'),
(4, 'Brother'),
(5, 'Sister'),
(6, 'Son'),
(7, 'Daughter'),
(8, 'Grandfather'),
(9, 'Grandmother'),
(10, 'Niece'),
(11, 'Nephew'),
(12, 'Husband'),
(13, 'Wife'),
(14, 'Aunt'),
(15, 'Uncle'),
(16, 'Relative'),
(17, 'Wife'),
(18, 'Friend'),
(19, 'Client');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_pin`
--
ALTER TABLE `m_pin`
  ADD PRIMARY KEY (`m_pin_id`);

--
-- Indexes for table `patient_registration`
--
ALTER TABLE `patient_registration`
  ADD PRIMARY KEY (`app_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_pin`
--
ALTER TABLE `m_pin`
  MODIFY `m_pin_id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `patient_registration`
--
ALTER TABLE `patient_registration`
  MODIFY `app_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
