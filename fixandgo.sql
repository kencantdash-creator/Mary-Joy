-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2026 at 03:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fixandgo`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `ref_code` varchar(12) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `brand_id` int(11) NOT NULL,
  `model` varchar(80) NOT NULL,
  `service_id` int(11) NOT NULL,
  `issue` text NOT NULL,
  `visit_type` enum('Drop-off','Repair appointment') NOT NULL,
  `appt_date` date NOT NULL,
  `appt_time` time NOT NULL,
  `status` enum('Scheduled','Waiting for Inspection','Being Repaired','Ready for Pickup','Completed') NOT NULL DEFAULT 'Scheduled',
  `est_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `ref_code`, `customer_name`, `phone`, `email`, `brand_id`, `model`, `service_id`, `issue`, `visit_type`, `appt_date`, `appt_time`, `status`, `est_price`, `created_at`) VALUES
(1, 'FG-DEMO01', 'Sample Customer', '09171234567', NULL, 2, 'Galaxy A54', 1, 'Cracked screen after a fall.', 'Drop-off', '2026-09-29', '10:00:00', 'Being Repaired', 3200.00, '2026-09-30 12:56:44'),
(2, 'FG-DEMO02', 'Sample Customer', '09171234567', NULL, 2, 'Galaxy A54', 2, 'Battery drains in two hours.', 'Repair appointment', '2026-08-14', '14:00:00', 'Completed', 1500.00, '2026-09-30 12:56:44'),
(3, 'FG-DEMO03', 'Sample Customer', '09171234567', NULL, 2, 'Galaxy A54', 4, 'Phone freezes on the logo screen.', 'Drop-off', '2026-07-02', '09:00:00', 'Completed', 400.00, '2026-09-30 12:56:44');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`) VALUES
(1, 'Apple'),
(3, 'Oppo'),
(6, 'Realme'),
(2, 'Samsung'),
(4, 'Vivo'),
(5, 'Xiaomi');

-- --------------------------------------------------------

--
-- Table structure for table `device_reports`
--

CREATE TABLE `device_reports` (
  `id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `model` varchar(80) NOT NULL,
  `age` varchar(30) NOT NULL,
  `category` varchar(60) NOT NULL,
  `description` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prices`
--

CREATE TABLE `prices` (
  `id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `est_hours` decimal(4,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prices`
--

INSERT INTO `prices` (`id`, `brand_id`, `service_id`, `price`, `est_hours`) VALUES
(1, 1, 1, 4500.00, 3.0),
(2, 1, 2, 2200.00, 1.5),
(3, 1, 3, 1800.00, 2.0),
(4, 1, 4, 500.00, 1.0),
(5, 2, 1, 3200.00, 3.0),
(6, 2, 2, 1500.00, 1.5),
(7, 2, 3, 1200.00, 2.0),
(8, 2, 4, 400.00, 1.0),
(9, 3, 1, 2400.00, 3.0),
(10, 3, 2, 1100.00, 1.5),
(11, 3, 3, 900.00, 2.0),
(12, 3, 4, 350.00, 1.0),
(13, 4, 1, 2300.00, 3.0),
(14, 4, 2, 1000.00, 1.5),
(15, 4, 3, 850.00, 2.0),
(16, 4, 4, 350.00, 1.0),
(17, 5, 1, 2000.00, 3.0),
(18, 5, 2, 950.00, 1.5),
(19, 5, 3, 800.00, 2.0),
(20, 5, 4, 350.00, 1.0),
(21, 6, 1, 1900.00, 3.0),
(22, 6, 2, 900.00, 1.5),
(23, 6, 3, 750.00, 2.0),
(24, 6, 4, 350.00, 1.0);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `icon`) VALUES
(1, 'Screen Replacement', 'Cracked, unresponsive or flickering display replaced with a tested panel.', 'smartphone'),
(2, 'Battery Replacement', 'New battery for phones that drain fast, shut down or swell.', 'battery'),
(3, 'Charging-Port Repair', 'Fixes loose, dirty or damaged ports that will not charge properly.', 'zap'),
(4, 'Software Troubleshooting', 'Fixes for freezing, boot loops, slow performance and failed updates.', 'cpu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ref_code` (`ref_code`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `device_reports`
--
ALTER TABLE `device_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `prices`
--
ALTER TABLE `prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_bs` (`brand_id`,`service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `device_reports`
--
ALTER TABLE `device_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prices`
--
ALTER TABLE `prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `device_reports`
--
ALTER TABLE `device_reports`
  ADD CONSTRAINT `device_reports_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);

--
-- Constraints for table `prices`
--
ALTER TABLE `prices`
  ADD CONSTRAINT `prices_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prices_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
